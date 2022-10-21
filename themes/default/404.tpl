{extends file='public.tpl'}
{block name=content}
    <section class="error-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="text-center">404</h1>
                    <div class="error-messages">
                        <p class="not-found text-center">{t}The page you are looking for was not found!{/t}</p>
                        {include 'errors.tpl'}
                        <div class="text-center">
                            <a href="{$routes->getRouteString("publicIndex")}" class="btn btn-primary shadow">{t}Back to home{/t}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/block}