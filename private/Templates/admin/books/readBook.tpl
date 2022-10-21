{extends file='admin/admin.tpl'}
{block name=title}{t}Read Book{/t}{/block}
{block name=toolbar}{/block}
{block name=headerCss append}
    {if $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'fb2'}
        <link rel="stylesheet" href="{$resourcePath}assets/js/plugins/fb2js/style.css">
    {elseif $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'epub'}
        <link rel="stylesheet" href="{$resourcePath}assets/js/plugins/epubjs/style.css">
    {/if}
{/block}
{block name=content}
    {if $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'pdf'}
        <iframe src="{$resourcePath}assets/js/plugins/pdfjs/web/viewer.html?file={$book->getEBook()->getWebPath()}" frameborder="0" width="100%" height="650" allowfullscreen=""></iframe>
    {elseif $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'epub'}
        <div style="height: 650px;width: 100%;">
            <div id="outerContainer">
                <div id="sidebarContainer">
                    <div id="toolbarSidebar">
                        <div class="splitToolbarButton toggled">
                            <button id="viewOutline" class="toolbarButton" title="{t}Show Document Outline (double-click to expand/collapse all items){/t}" tabindex="3">
                                <span>{t}Document Outline{/t}</span>
                            </button>
                        </div>
                    </div>
                    <div id="sidebarContent">
                        <div id="tocView">
                        </div>
                    </div>
                    <div id="sidebarResizer" class="hidden"></div>
                </div>
                <div id="mainContainer">
                    <div class="toolbar">
                        <div id="toolbarContainer">
                            <div id="toolbarViewer">
                                <div id="toolbarViewerLeft">
                                    <button id="sidebarToggle" class="toolbarButton" title="{t}Toggle Sidebar{/t}" tabindex="11">
                                        <span>{t}Toggle Sidebar{/t}</span>
                                    </button>
                                    <div class="toolbarButtonSpacer"></div>
                                    <div class="splitToolbarButton">
                                        <button class="toolbarButton pageUp" title="{t}Previous Page{/t}" id="previous" tabindex="13" onclick="Book.prevPage();">
                                            <span>{t}Previous{/t}</span>
                                        </button>
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button class="toolbarButton pageDown" title="{t}Next Page{/t}" id="next" tabindex="14" onclick="Book.nextPage();">
                                            <span>{t}Next{/t}</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="toolbarViewerRight">
                                    <button id="presentationMode" class="toolbarButton presentationMode" title="{t}Switch to Presentation Mode{/t}" tabindex="31">
                                        <span>{t}Presentation Mode{/t}</span>
                                    </button>
                                </div>
                                <div id="toolbarViewerMiddle">
                                    <div class="splitToolbarButton">
                                        <button id="zoomOut" class="toolbarButton zoomOut" title="{t}Zoom Out{/t}" tabindex="21">
                                            <span>{t}Zoom Out{/t}</span>
                                        </button>
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button id="zoomIn" class="toolbarButton zoomIn" title="{t}Zoom In{/t}" tabindex="22">
                                            <span>{t}Zoom In{/t}</span>
                                        </button>
                                    </div>
                                    <div class="splitToolbarButton">
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button id="zoomReset" class="toolbarButton zoomReset" title="{t}Zoom Reset{/t}" tabindex="23">
                                            <span>{t}Zoom Reset{/t}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="viewerContainer" tabindex="0">
                        <div id="viewer" class="pdfViewer">
                            <div id="area"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {elseif $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'fb2'}
        <div style="height: 650px;width: 100%;">
            <div id="outerContainer">
                <div id="sidebarContainer">
                    <div id="toolbarSidebar">
                        <div class="splitToolbarButton toggled">
                            <button id="viewOutline" class="toolbarButton" title="{t}Show Document Outline (double-click to expand/collapse all items){/t}" tabindex="3">
                                <span>{t}Document Outline{/t}</span>
                            </button>
                        </div>
                    </div>
                    <div id="sidebarContent">
                        <div id="tocView">
                        </div>
                    </div>
                    <div id="sidebarResizer" class="hidden"></div>
                </div>
                <div id="mainContainer">
                    <div class="toolbar">
                        <div id="toolbarContainer">
                            <div id="toolbarViewer">
                                <div id="toolbarViewerLeft">
                                    <button id="sidebarToggle" class="toolbarButton" title="{t}Toggle Sidebar{/t}" tabindex="11">
                                        <span>{t}Toggle Sidebar{/t}</span>
                                    </button>
                                    <div class="toolbarButtonSpacer"></div>
                                    <div class="splitToolbarButton">
                                        <button class="toolbarButton pageUp" title="{t}Previous Page{/t}" id="previous" tabindex="13">
                                            <span>{t}Previous{/t}</span>
                                        </button>
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button class="toolbarButton pageDown" title="{t}Next Page{/t}" id="next" tabindex="14">
                                            <span>{t}Next{/t}</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="toolbarViewerRight">
                                    <button id="presentationMode" class="toolbarButton presentationMode" title="{t}Switch to Presentation Mode{/t}" tabindex="31">
                                        <span>{t}Presentation Mode{/t}</span>
                                    </button>
                                </div>
                                <div id="toolbarViewerMiddle">
                                    <div class="splitToolbarButton">
                                        <button id="zoomOut" class="toolbarButton zoomOut" title="{t}Zoom Out{/t}" tabindex="21">
                                            <span>{t}Zoom Out{/t}</span>
                                        </button>
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button id="zoomIn" class="toolbarButton zoomIn" title="{t}Zoom In{/t}" tabindex="22">
                                            <span>{t}Zoom In{/t}</span>
                                        </button>
                                    </div>
                                    <div class="splitToolbarButton">
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button id="zoomReset" class="toolbarButton zoomReset" title="{t}Zoom Reset{/t}" tabindex="23">
                                            <span>{t}Zoom Reset{/t}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="viewerContainer" tabindex="0">
                        <div id="viewer" class="pdfViewer">
                            <div id="area"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {else}
        {t}This eBook format is not supported yet.{/t}
    {/if}
{/block}
{block name=footerPageJs append}
    {if $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'fb2'}
        <script type="text/javascript" src="{$resourcePath}assets/js/plugins/fb2js/fb2-reader.js"></script>
        <script type="text/javascript" src="{$resourcePath}assets/js/plugins/epubjs/screenfull.min.js"></script>
    {elseif $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'epub'}
        <script type="text/javascript" src="{$resourcePath}assets/js/plugins/epubjs/zip.min.js"></script>
        <script type="text/javascript" src="{$resourcePath}assets/js/plugins/epubjs/epub.min.js"></script>
        <script type="text/javascript" src="{$resourcePath}assets/js/plugins/epubjs/screenfull.min.js"></script>
    {/if}
{/block}
{block name=footerCustomJs append}
    {if $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'fb2'}
        <script>
            var outerContainer = $('#outerContainer');
            $('#sidebarToggle').on('click', function (e) {
                $(this).toggleClass('toggled');
                $(outerContainer).toggleClass('sidebarOpen');
            });

            $fullscreen = $('#presentationMode');
            if (typeof screenfull !== 'undefined') {
                console.log(screenfull);
                $fullscreen.on("click", function () {
                    screenfull.toggle($('#outerContainer')[0]);
                });
            }
            $("#zoomIn").on('click', function (e) {
                ZoomIn();
            });
            $("#zoomOut").on('click', function (e) {
                ZoomOut();
            });
            $("#zoomReset").on('click', function (e) {
                $("#area").width('816px');
                $('#area').css("font-size", '100%');
            });

            var defaultPercentage = 100;
            var stepPercentage = 10;
            function ZoomIn() {
                $("#area").width($("#area").width() + 150);
                //$("#area").height($("#area").height() + 150);
                var size = $('#area').attr('data-size');
                if (!size) {
                    size = defaultPercentage;
                    $('#area').attr('data-size', defaultPercentage);
                }
                var fontSize = (parseInt(size) + stepPercentage) + "%";
                $('#area').attr('data-size', (parseInt(size) + stepPercentage));
                $('#area').css("font-size", fontSize);
            }
            function ZoomOut() {
                $("#area").width($("#area").width() - 150);
                //$("#area").height($("#area").height() - 150);
                var size = $('#area').attr('data-size');
                if (!size) {
                    size = defaultPercentage;
                    $('#area').attr('data-size', defaultPercentage);
                }
                var fontSize = (parseInt(size) - stepPercentage) + "%";
                $('#area').attr('data-size', (parseInt(size) - stepPercentage));
                $('#area').css("font-size", fontSize);
            }
            function setup_nav_links(toc, current_section) {
                var prev_link = document.getElementById("previous");
                var next_link = document.getElementById("next");
                if (current_section <= 0) {
                    prev_link.style.display = "none";
                } else {
                    prev_link.style.display = "inline";
                }
                if (current_section >= (toc.length - 1)) {
                    next_link.style.display = "none";
                } else {
                    next_link.style.display = "inline";
                }
            }
            function setup_toc(toc, book) {
                document.getElementById("tocView").innerHTML = "";
                for (var i = 0; i < toc.length; i++) {
                    var div = document.createElement("div");
                    div.setAttribute('class', 'outlineItem');
                    document.getElementById("tocView").appendChild(div);

                    var a = document.createElement("a");
                    a.href = "#area";
                    a.onclick = toc_handler(toc, i, book);
                    div.appendChild(a);

                    var title = FicR.getSectionTitle(toc[i]);
                    if (!title) title = "(unnamed)";
                    a.innerHTML = title;
                }
            }

            var current_section = 0;

            function toc_handler(toc, section_num, book) {
                return function () {
                    load_section(toc[section_num], book);
                    setup_nav_links(toc, section_num);
                    current_section = section_num;
                    //return false; // Let the anchor work.
                }
            }

            function load_section(section, book) {
                var content = document.getElementById("area");
                content.innerHTML = "";
                content.appendChild(FicR.render_section(section, book));
            }

            function loadDoc() {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "{$book->getEBook()->getWebPath()}", false);
                xhttp.send();
                fb2(xhttp);
            }
            function fb2(xml) {
                var xmlDoc = xml.responseXML;
                var parser = new DOMParser();
                xmlDoc = parser.parseFromString(xml.responseText, "application/xml");
                {*document.getElementById("title").innerHTML =
                        xmlDoc.getElementsByTagName("book-title")[0].childNodes[0].nodeValue;*}

                var booksrc = parser.parseFromString(
                        xml.responseText, "application/xml");
                var book = new FictionReader(booksrc);
                var toc = book.getTableOfContents();

                setup_toc(toc, book);
                load_section(toc[0], book);
                setup_nav_links(toc, current_section);

                var prev_link = document.getElementById("previous");
                prev_link.addEventListener("click", function () {
                    console.log(current_section);
                    console.log('previous');
                    if (current_section <= 0) {
                        alert("You're at the beginning of the document.");
                    } else {
                        current_section--;
                        load_section(toc[current_section], book);
                        setup_nav_links(toc, current_section);
                    }
                }, false);
                var next_link = document.getElementById("next");
                next_link.addEventListener("click", function () {
                    console.log(current_section);
                    console.log('next');
                    if (current_section >= (toc.length - 1)) {
                        alert("You're at the end of the document.");
                    } else {
                        current_section++;
                        load_section(toc[current_section], book);
                        setup_nav_links(toc, current_section);
                    }
                }, false);
            }
            loadDoc();
        </script>
    {elseif $book->getEBook() != null and $book->getEBook()->getPath()|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'epub'}
        <script>
            var outerContainer = $('#outerContainer');
            $('#sidebarToggle').on('click', function (e) {
                $(this).toggleClass('toggled');
                $(outerContainer).toggleClass('sidebarOpen');
            });

            $fullscreen = $('#presentationMode');
            if (typeof screenfull !== 'undefined') {
                console.log(screenfull);
                $fullscreen.on("click", function () {
                    screenfull.toggle($('#outerContainer')[0]);
                });
            }
            $("#zoomIn").on('click', function (e) {
                ZoomIn();
            });
            $("#zoomOut").on('click', function (e) {
                ZoomOut();
            });
            $("#zoomReset").on('click', function (e) {
                $("#area").width('816px');
                $("#area").height('1056px');
            });

            var defaultPercentage = 100;
            var stepPercentage = 10;
            function ZoomIn() {
                $("#area").width($("#area").width() + 150);
                $("#area").height($("#area").height() + 150);
                var size = $('#area').attr('data-size');
                if (!size) {
                    size = defaultPercentage;
                    $('#area').attr('data-size', defaultPercentage);
                }
                var fontSize = (parseInt(size) + stepPercentage) + "%";
                $('#area').attr('data-size', (parseInt(size) + stepPercentage));
                Book.setStyle("font-size", fontSize);
            }

            function ZoomOut() {
                $("#area").width($("#area").width() - 150);
                $("#area").height($("#area").height() - 150);
                var size = $('#area').attr('data-size');
                if (!size) {
                    size = defaultPercentage;
                    $('#area').attr('data-size', defaultPercentage);
                }
                var fontSize = (parseInt(size) - stepPercentage) + "%";
                $('#area').attr('data-size', (parseInt(size) - stepPercentage));
                Book.setStyle("font-size", fontSize);
            }

            var Book = ePub("{$book->getEBook()->getWebPath()}", {
                spreads: false,
                styles: {
                    padding: '25px'
                }
            });
            Book.forceSingle();
            Book.renderTo("area");
            Book.setStyle("font-size", "100%");

            var generateTocItems = function (toc, level) {
                var container = document.createElement("div");

                if (!level) level = 1;

                toc.forEach(function (chapter) {
                    var listitem = document.createElement("div"),
                            link = document.createElement("a");
                    toggle = document.createElement("a");
                    listitem.setAttribute('class', 'outlineItem');
                    var subitems;

                    listitem.id = "toc-" + chapter.id;
                    listitem.classList.add('list_item');

                    link.textContent = chapter.label;
                    link.href = chapter.href;

                    link.classList.add('toc_link');

                    listitem.appendChild(link);

                    if (chapter.subitems.length > 0) {
                        level++;
                        subitems = generateTocItems(chapter.subitems, level);
                        toggle.classList.add('toc_toggle');

                        listitem.insertBefore(toggle, link);
                        listitem.appendChild(subitems);
                    }


                    container.appendChild(listitem);

                });

                return container;
            };

            Book.getToc().then(function (toc) {
                var tocItems = generateTocItems(toc);
                $("#tocView").append(tocItems);
            });

            $(document).on('click', '.toc_link', function (e) {
                e.preventDefault();
                Book.goto($(this).attr('href'));
            });

        </script>
    {/if}
{/block}