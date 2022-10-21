{extends file='public.tpl'}
{block name=content}
    <section class="error-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="text-center">403</h1>
                    <div class="error-messages">
                        {include 'errors.tpl'}
                        <div class="text-center">
                            <a href="#" class="btn btn-primary shadow" onclick="history.back(); return false;">{t}Go back!{/t}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/block}