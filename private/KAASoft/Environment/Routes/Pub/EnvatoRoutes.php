<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Pub;


    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class EnvatoRoutes implements RoutesInterface {
        const ENVATO_LICENSE_VERIFICATION_PUBLIC_ROUTE = "envatoLicenseVerificationPublic";

        public static function getRoutes() {
            $routes = [];

            $routes[EnvatoRoutes::ENVATO_LICENSE_VERIFICATION_PUBLIC_ROUTE] = new PublicRoute(_("Envato License Verification"),
                                                                                              "/envato/verify-purchase-code[/]??",
                                                                                              "Pub\\Envato\\EnvatoPurchaseVerifyAction",
                                                                                              "/envato/verify-purchase-code");

            return $routes;
        }
    }
