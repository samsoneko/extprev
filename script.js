class ExtPrevPlugin {

    #preview = null;
    #abortController = null;

    constructor() {
        this.#preview = document.createElement('div');
        this.#preview.classList.add('plugin-extprev');
        this.#preview.style.position = 'absolute';
        this.#preview.style.top = '0';
        this.#preview.style.left = '0';
        this.#preview.style.display = 'none';
        document.body.append(this.#preview);
    }

    attach() {
        const selector = JSINFO.plugin.extprev.selector;
        const links = document.querySelectorAll(selector + ' a.urlextern');
        links.forEach(link => {
            link.addEventListener('mouseenter', this.onMouseEnter.bind(this));
            link.addEventListener('mouseleave', this.onMouseLeave.bind(this));
            link.removeAttribute('title');
        });
    }

    async loadPreview(href) {
        const url = encodeURIComponent(href);
        try {
            if (this.#abortController !== null) this.#abortController.abort();
            this.#abortController = new AbortController();

            const dataPromise = await fetch(`/lib/plugins/extprev/PreviewLoader.php?url=${url}`,
                {
                    signal: this.#abortController.signal,
                    method: 'POST',
                }
            );
            this.#preview.style.display = 'block';
            const data = await dataPromise.json();
            this.renderPreview(data);
        } catch (error) {
            this.#preview.innerHTML = await data.text();
            this.#preview.style.display = 'block';
        }
    }

    renderPreview(data) {
        this.#preview.innerHTML = "";

        if(data.hasOwnProperty("error")) {
            this.#preview.innerHTML = "<p>" + data.error + "</p>";
            return
        }

        if(data.hasOwnProperty("sitename") && data.sitename != "") {
            this.#preview.innerHTML = "<p>" + data.sitename + "</p>";
        }
        if(data.hasOwnProperty("title") && data.title != "") {
            this.#preview.innerHTML += "<h3>" + data.title + "</h3>";
        }
        if(data.hasOwnProperty("description") && data.description != "") {
            this.#preview.innerHTML += "<p>"+ data.description + "</p>";
        }
        if(data.hasOwnProperty("image") && data.image != "") {
            this.#preview.innerHTML += "<img src=" + data.image + ' alt="" />';
        }
    }

    async onMouseEnter(e) {
        this.#preview.style.top = e.pageY + 10 + 'px';
        this.#preview.style.left = e.pageX + 10 + 'px';
        await this.loadPreview(e.target.getAttribute("href"));
    }

    onMouseLeave(e) {
        this.#preview.style.display = 'none';
        if (this.#abortController !== null) this.#abortController.abort();
        this.#abortController = null;
        this.#preview.innerHTML = "";
    }

}


document.addEventListener('DOMContentLoaded', () => {
    const extprev = new ExtPrevPlugin();
    extprev.attach();
});
