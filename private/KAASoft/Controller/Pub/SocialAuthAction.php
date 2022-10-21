<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use Exception;
    use KAASoft\Controller\Admin\Image\ImageDatabaseHelper;
    use KAASoft\Controller\Admin\Image\ImageUploadBaseAction;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\SocialNetworkSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\HTTP\HttpClient;
    use KAASoft\Util\Message;
    use KAASoft\Util\ValidationHelper;
    use SocialConnect\Auth\Service;
    use SocialConnect\Common\Http\Client\Curl;
    use SocialConnect\Provider\AbstractBaseProvider;
    use SocialConnect\Provider\Session\Session;

    /**
     * Class SocialAuthAction
     * @package KAASoft\Controller\Pub
     */
    class SocialAuthAction extends PublicActionBase {
        /**
         * SocialAuthAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $providerName = ValidationHelper::getString($args["providerId"]);
            $httpClient = new Curl();

            $socialNetworkSetting = new SocialNetworkSettings();
            $socialNetworkSetting->loadSettings();

            $providers = $socialNetworkSetting->getConfig();
            ///SocialAuthAction::getConfiguredProviders();
            $collectionFactory = null;
            $service = new Service($httpClient,
                                   new Session(),
                                   $providers,
                                   $collectionFactory);


            $provider = $service->getProvider($providerName);

            if (ValidationHelper::isArrayEmpty($_GET)) {

                $authUrl = $provider->makeAuthUrl();

                Helper::redirectTo($authUrl);
                exit( 0 );
            }
            else {
                $accessToken = null;
                try {
                    $accessToken = $this->authorize($provider);
                }
                catch (Exception $e) {
                    $accessToken = null;
                }

                if ($accessToken !== null) {
                    $socialUser = $provider->getIdentity($accessToken);

                    if ($this->startDatabaseTransaction()) {
                        $userDatabaseHelper = new UserDatabaseHelper($this);

                        $user = $userDatabaseHelper->getUserBySocialNetwork($providerName,
                                                                            $socialUser->id);

                        $user = $this->copySocialUserToUser($providerName,
                                                            $socialUser,
                                                            $user);

                        if ($user === null) {
                            $this->rollbackDatabaseChanges();

                            return new DisplaySwitch(null,
                                                     $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_LOGIN_ROUTE));
                        }
                        $userPhoto = $user->getPhoto();
                        if ($userPhoto !== null and $userPhoto instanceof Image) {
                            $imageDatabaseHelper = new ImageDatabaseHelper($this);
                            $photoId = $imageDatabaseHelper->saveImage($userPhoto);
                            if ($photoId !== false) {
                                $user->setPhotoId($photoId);
                            }
                        }
                        $resultOrUserId = $userDatabaseHelper->saveUser($user,
                                                                ValidationHelper::isEmpty($user->getId()));
                        if ($resultOrUserId === false) {
                            $this->rollbackDatabaseChanges();
                            \KAASoft\Environment\Session::addSessionMessage(_("Couldn't save user in database."),
                                                                            Message::MESSAGE_STATUS_ERROR);

                            return new DisplaySwitch(null,
                                                     $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_LOGIN_ROUTE));
                        }
                        if($user->getId() === null){
                            $user->setId($resultOrUserId);
                        }

                        $this->commitDatabaseChanges();
                        \KAASoft\Environment\Session::addSessionValue(\KAASoft\Environment\Session::USER,
                                                                      $user);

                        $lastRoute = \KAASoft\Environment\Session::getSessionValue(\KAASoft\Environment\Session::LAST_ROUTE,
                                                                                   $this->routeController->getRouteString(GeneralPublicRoutes::USER_PROFILE_ROUTE_NAME));

                        if (strcmp($lastRoute,
                                   $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_LOGIN_ROUTE)) !== 0
                        ) {
                            Helper::redirectTo($lastRoute);
                        }
                        else {
                            Helper::redirectTo($this->routeController->getRouteString(GeneralPublicRoutes::USER_PROFILE_ROUTE_NAME));
                        }

                        return new DisplaySwitch(null,
                                                 $lastRoute);


                    }
                    else {
                        \KAASoft\Environment\Session::addSessionMessage(_("Couldn't start database transaction."),
                                                                        Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_LOGIN_ROUTE));
                    }
                }
                else {
                    \KAASoft\Environment\Session::addSessionMessage(_("Couldn't identify user."),
                                                                    Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_LOGIN_ROUTE));
                }
            }
        }

        /**
         * @param      $provider
         * @param      $socialUser \SocialConnect\Common\Entity\User
         * @param User $user
         * @return User
         */
        protected function copySocialUserToUser($provider, $socialUser, $user = null) {
            // if new user
            if ($user === null) {
                $user = new User();
                // socialId
                if (!ValidationHelper::isEmpty($socialUser->id)) {
                    $user->setSocialId($socialUser->id);
                }
                else {
                    \KAASoft\Environment\Session::addSessionMessage(sprintf(_("There is no ID in <b>%s</b> response"),
                                                                            $provider),
                                                                    Message::MESSAGE_STATUS_ERROR);

                    return null;
                }
                // email
                if (!ValidationHelper::isEmpty($socialUser->email)) {
                    $user->setEmail($socialUser->email);
                }
                // provider
                $user->setProvider($provider);
                // user name
                if (!ValidationHelper::isEmpty($socialUser->firstname)) {
                    $user->setFirstName($socialUser->firstname);
                }
                if (!ValidationHelper::isEmpty($socialUser->lastname)) {
                    $user->setLastName($socialUser->lastname);
                }
                if (ValidationHelper::isEmpty($user->getFirstName()) and ValidationHelper::isEmpty($user->getLastName())) {
                    $user->setFirstName($socialUser->fullname);
                }
                // birthday
                /*if (!ValidationHelper::isEmpty($socialUser->birthday)) {
                    $user->setBirthday($socialUser->birthday);
                }*/

                if (!ValidationHelper::isEmpty($socialUser->pictureURL)) {
                    $photo = $this->processUserPhoto($socialUser->pictureURL);
                    $user->setPhoto($photo);
                }
                // set some additional required fields
                $user->setIsActive(true);
                $user->setIsLdapUser(false);
                $user->setRoleId(Role::BUILTIN_USER_ROLES[Role::READER_ROLE_ID]);
                $user->setCreationDateTime(Helper::getDateTimeString());
                $user->setUpdateDateTime(Helper::getDateTimeString());
            }
            else { // if user already exists
                // email
                if (!ValidationHelper::isEmpty($socialUser->email)) {
                    $user->setEmail($socialUser->email);
                }
                // user name
                if (!ValidationHelper::isEmpty($socialUser->firstname)) {
                    $user->setFirstName($socialUser->firstname);
                }
                if (!ValidationHelper::isEmpty($socialUser->lastname)) {
                    $user->setLastName($socialUser->lastname);
                }
                if (ValidationHelper::isEmpty($user->getFirstName()) and ValidationHelper::isEmpty($user->getLastName())) {
                    $user->setFirstName($socialUser->fullname);
                }
                // birthday
                /*if (!ValidationHelper::isEmpty($socialUser->birthday)) {
                    $user->setBirthday($socialUser->birthday);
                }*/
                $user->setUpdateDateTime(Helper::getDateTimeString());
            }

            $role = new Role(Role::BUILTIN_USER_ROLES[Role::READER_ROLE_ID]);
            $user->setRole($role);

            return $user;
        }

        /**
         * @param $provider AbstractBaseProvider
         * @return mixed
         */
        protected function authorize($provider) {
            return $provider->getAccessTokenByRequestParameters($_GET);
        }

        /**
         * @param $photoPath
         * @return Image|null
         */
        private function processUserPhoto($photoPath) {
            $image = new Image();
            if (!ValidationHelper::isEmpty($photoPath)) {

                $userPhotoLocation = FileHelper::getImageCurrentMonthLocation(FileHelper::getUserPhotoLocation());
                $destFolder = FileHelper::getUniqueFolderName($userPhotoLocation);

                FileHelper::createDirectory($destFolder);
                $localFileName = FileHelper::getUniqueFileName($destFolder,
                                                               basename(FileHelper::getTempFileName()));

                $httpClient = new HttpClient();
                if ($httpClient->fetch($photoPath) === true) {
                    $fileContent = $httpClient->getResults();
                }
                else {
                    // ignore if we couldn't download photo
                    return null;
                }

                $result = FileHelper::createFile($localFileName);
                if ($result === false) {
                    // ignore if we couldn't create photo file
                    return null;
                }

                $result = file_put_contents($localFileName,
                                            $fileContent);

                if ($result === false) {
                    // ignore if we couldn't save photo file
                    return null;
                }

                $image->setPath(FileHelper::getRelativePath(FileHelper::getImageRootLocation(),
                                                            $localFileName));
                $image->setUploadingDateTime(Helper::getDateTimeString());
                $image->setIsGallery(true);

                ImageUploadBaseAction::resizeImages($localFileName,
                                                    ImageUploadBaseAction::getUserPhotoResolutions());

                return $image;
            }

            return null;
        }
    }