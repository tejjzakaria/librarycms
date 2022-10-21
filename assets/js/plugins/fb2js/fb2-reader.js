// FictionBook (a.k.a. FB2) reading library
// 2013-03-28 Felix Pleșoianu <felixp7@yahoo.com>
// MIT license -- see the main file for full text.

var FictionReader = function (xml) {
    this.xml = xml;

    this.toc = null;

    this.getBookTitle = function () {
        return this.xml.documentElement
            .getElementsByTagName("book-title")[0]
            .textContent;
    }

    this.getBookAuthors = function () {
        return this.getAuthorsFromElementId("title-info");
    }

    this.getDocumentAuthors = function () {
        return this.getAuthorsFromElementId("document-info");
    }

    this.getAuthorsFromElementId = function (id) {
        var author_data = [];
        try {
            var authors = this.xml.documentElement
                .getElementsByTagName(id)[0]
                .getElementsByTagName("author");
            for (var i = 0; i < authors.length; i++) {
                author_data.push(
                    FicR.getAuthorData(
                        authors[i]));
            }
        } catch (e) {
            console.log(e);
        }
        return author_data;
    }

    this.getBookAnnotation = function () {
        try {
            var annotation = this.xml.documentElement
                .getElementsByTagName("title-info")[0]
                .getElementsByTagName("annotation")[0];
            return FicR.render_section(annotation);
        } catch (e) {
            console.log(e);
            return document.createElement("div");
        }
    }

    this.getProgramUsed = function () {
        try {
            var program = this.xml.documentElement
                .getElementsByTagName("document-info")[0]
                .getElementsByTagName("program-used")[0];
            return program.textContent;
        } catch (e) {
            console.log(e);
            return "";
        }
    }

    this.getDocumentDate = function () {
        try {
            var date = this.xml.documentElement
                .getElementsByTagName("document-info")[0]
                .getElementsByTagName("date")[0];
            return date.textContent;
        } catch (e) {
            console.log(e);
            return "";
        }
    }

    this.getTableOfContents = function () {
        if (this.toc != null) return this.toc;

        this.toc = [];
        var body = this.xml.documentElement
            .getElementsByTagName("body")[0];
        var nodes = body.childNodes;
        for (var i = 0; i < nodes.length; i++) {
            if (!nodes[i].tagName) continue;
            if (nodes[i].tagName != "section") continue;

            this.toc.push(nodes[i]);
        }
        return this.toc;
    }

    this.getNotes = function () {
        var bodies = this.xml.documentElement
            .getElementsByTagName("body");
        var note_blocks = [];
        for (var i = 1; i < bodies.length; i++) {
            var name = bodies[i].getAttribute("name");
            if (name.search("notes") >= 0) {
                note_blocks.push(bodies[i]);
            }
        }
        return note_blocks;
    }


    this.getNote = function (id) {
        var noteId = this.getNotes(id);
        var note_text = this.xml.documentElement
            .getElementById(noteId);
        return note_text;
    }

    this.getImage = function (id) {
        var URL = window.URL = window.URL || window.webkitURL;
        var img = document.createElement("img");
        try {
            var binary = this.getBinaryById(id);
            var base64 = binary.textContent.replace(/\s+/gm, "");
            var contentType = binary.attributes['content-type'].textContent;
            var src = 'data:' + contentType + ';base64, ' + base64 + '';
            img.src = src;
            img.onload = function () {
                URL.revokeObjectURL(this.src);
            }
        } catch (e) {
            if (!binary)
                console.log("Failed to retrieve element " + id);
            else
                console.log(e);
        }
        return img;
    }

    // Idiotic workaround for the fact that browsers apparently
    // don't handle getElementById() for XML.
    this.getBinaryById = function (id) {
        var binaries = this.xml.documentElement
            .getElementsByTagName("binary");
        for (var i = 0; i < binaries.length; i++) {
            if (binaries[i].getAttribute("id") == id)
                return binaries[i];
        }
        return null;
    }
};

var FicR = {};

FicR.getSectionTitle = function (section) {
    var tmp = section.getElementsByTagName("title")[0];
    if (!tmp) return null;
    return tmp.textContent;
};

FicR.getAuthorData = function (author) {
    var first_name = author.getElementsByTagName("first-name")[0];
    var middle_name = author.getElementsByTagName("middle-name")[0];
    var last_name = author.getElementsByTagName("last-name")[0];
    var nickname = author.getElementsByTagName("nickname")[0];
    first_name = first_name ? first_name.textContent : "";
    middle_name = middle_name ? middle_name.textContent : "";
    last_name = last_name ? last_name.textContent : "";
    nickname = nickname ? nickname.textContent : "";
    return {
        first_name: first_name,
        middle_name: middle_name,
        last_name: last_name,
        nickname: nickname
    };
}

FicR.render_section = function (section, book) {
    var s;
    if (section.tagName == "section")
        s = document.createElement("section");
    else if (section.tagName == "cite")
        s = document.createElement("blockquote");
    else if (section.tagName == "td")
        s = document.createElement("td");
    else
        s = document.createElement("div");

    if (section.tagName == "annotation")
        s.className = "annotation";
    else if (section.tagName == "epigraph")
        s.className = "epigraph";

    var nodes = section.childNodes;
    for (var i = 0; i < nodes.length; i++) {
        if (!nodes[i].tagName) continue;
        if (nodes[i].tagName == "p") {
            s.appendChild(FicR.render_para(nodes[i], book));
        } else if (nodes[i].tagName == "empty-line") {
            s.appendChild(document.createElement("br"));
        } else if (nodes[i].tagName == "subtitle") {
            var h3 = document.createElement("h3");
            h3.innerHTML = nodes[i].textContent;
            s.appendChild(h3);
            if (nodes[i].hasAttribute("id"))
                h3.id = nodes[i].getAttribute("id");
        } else if (nodes[i].tagName == "section") {
            s.appendChild(FicR.render_section(nodes[i]));
        } else if (nodes[i].tagName == "cite") {
            s.appendChild(FicR.render_section(nodes[i]));
        } else if (nodes[i].tagName == "poem") {
            s.appendChild(FicR.render_poem(nodes[i]));
        } else if (nodes[i].tagName == "title") {
            var title = document.createElement("h2");
            s.appendChild(title);
            title.innerHTML = nodes[i].textContent;
        } else if (nodes[i].tagName == "annotation") {
            s.appendChild(FicR.render_section(nodes[i]));
        } else if (nodes[i].tagName == "epigraph") {
            s.appendChild(FicR.render_section(nodes[i]));
        } else if (nodes[i].tagName == "table") {
            s.appendChild(FicR.render_table(nodes[i]));
        } else {
            s.appendChild(
                document.createTextNode(
                    nodes[i].textContent));
        }
    }

    return s;
};
FicR.render_para = function (para, book) {
    var p = document.createElement("p");
    var nodes = para.childNodes;
    for (var i = 0; i < nodes.length; i++) {
        if (nodes[i].nodeType == 3) {
            p.appendChild(nodes[i].cloneNode());
        } else if (nodes[i].nodeType != 1) {
            continue;
        } else if (nodes[i].tagName == "emphasis") {
            var em = document.createElement("em");
            em.innerHTML = nodes[i].textContent;
            p.appendChild(em);
        } else if (nodes[i].tagName == "strong") {
            var strong = document.createElement("strong");
            strong.innerHTML = nodes[i].textContent;
            p.appendChild(strong);
        } else if (nodes[i].tagName == "image") {
            var imgid =
                nodes[i].getAttribute("xlink:href").slice(1);
            if(book) {
                p.appendChild(book.getImage(imgid));
            }
        } else if (nodes[i].tagName == "a") {
            /*var a = document.createElement("a");
            a.innerHTML = nodes[i].textContent;
            a.href = nodes[i].getAttribute("l:href").slice(1);
            var id = nodes[i].getAttribute("l:href").slice(1);
            a.className = "note";
            //console.log(FictionReader);
            p.appendChild(a);*/
        } else {
            p.appendChild(
                document.createTextNode(
                    nodes[i].textContent));
        }
    }

    return p;
};

FicR.render_poem = function (section) {
    var poem = document.createElement("div");
    poem.className = "poem";

    var stanzas = section.childNodes;
    for (var i = 0; i < stanzas.length; i++) {
        if (!stanzas[i].tagName) continue;
        if (stanzas[i].tagName != "stanza") continue;

        var stanza = document.createElement("p");

        var verses = stanzas[i].childNodes;
        for (var j = 0; j < verses.length; j++) {
            if (!verses[j].tagName) continue;
            if (verses[j].tagName != "v") continue;

            stanza.appendChild(
                document.createTextNode(
                    verses[j].textContent));
            stanza.appendChild(document.createElement("br"));
        }

        poem.appendChild(stanza);
    }

    return poem;
};

FicR.render_table = function (section) {
    var table = document.createElement("table");

    var rows = section.childNodes;
    for (var i = 0; i < rows.length; i++) {
        if (!rows[i].tagName) continue;
        if (rows[i].tagName != "tr") continue;

        var row = document.createElement("tr");

        var cells = rows[i].childNodes;
        for (var j = 0; j < cells.length; j++) {
            if (!cells[j].tagName) continue;
            if (cells[j].tagName != "td") continue;

            row.appendChild(FicR.render_section(cells[j]));
        }

        table.appendChild(row);
    }

    return table;
};
