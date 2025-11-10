<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
    </head>
    <body class="p-6">
        <div id="app" class="space-y-4">
            <form id="filters" class="space-x-2">
                <input name="q" placeholder="Search..." />
                <select name="category">
                    <option value="">All</option>
                    <option value="batteries">Batteries</option>
                    <option value="solar-panels">Solar Panels</option>
                    <option value="connectors">Connectors</option>
                </select>
                <input name="price_min" type="number" step="0.01" placeholder="min price" />
                <input name="price_max" type="number" step="0.01" placeholder="max price" />
                <button>Apply</button>
            </form>
            <pre id="out">Loading…</pre>
        </div>
        <script>
            async function load() {
                const params = new URLSearchParams(new FormData(document.getElementById('filters')));
                const res = await fetch('/api/products?' + params.toString());
                const data = await res.json();
                document.getElementById('out').textContent = JSON.stringify(data, null, 2);
            }
            document.getElementById('filters').addEventListener('submit', e => { e.preventDefault(); load(); });
            load();
        </script>
    </body>
</html>
