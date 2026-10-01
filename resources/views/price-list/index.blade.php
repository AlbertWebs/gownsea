<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Price list | Gownsea</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;color:#29221d;background:#f7f5f1;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0}button,input{font:inherit}.wrap{width:min(1100px,100%);margin:auto;padding:clamp(16px,4vw,40px)}
        .brand{font-size:13px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:#8d694e}.hero{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin:12px 0 24px}
        h1{font-family:Georgia,serif;font-size:clamp(30px,5vw,44px);font-weight:500;margin:0 0 7px}.sub{margin:0;color:#776f68;font-size:14px;line-height:1.5}.panel{background:white;border:1px solid #e9e4dd;border-radius:16px;box-shadow:0 12px 35px #3022140a}
        .lock{max-width:440px;margin:12vh auto;padding:28px}.lock h1{font-size:32px}.lock form{display:flex;gap:10px;margin-top:20px}.pin{width:100%;min-width:0;border:1px solid #d9d2c8;border-radius:10px;padding:12px 14px;letter-spacing:.2em}
        .button{border:0;background:#704b37;color:white;border-radius:10px;padding:12px 18px;font-weight:700;cursor:pointer;white-space:nowrap}.button:disabled{opacity:.55;cursor:wait}
        .tools{display:flex;gap:12px;padding:16px;border-bottom:1px solid #eee9e2}.search{flex:1;min-width:0;border:1px solid #e1dbd3;border-radius:10px;padding:11px 13px}.count{align-self:center;color:#776f68;font-size:13px;white-space:nowrap}
        .table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:760px}th{text-align:left;color:#766e66;font-size:11px;letter-spacing:.08em;text-transform:uppercase;font-weight:700;background:#fcfbf9}th,td{padding:14px 16px;border-bottom:1px solid #eee9e2}tbody tr:last-child td{border-bottom:0}.product{font-weight:700}.category{display:block;font-size:12px;color:#827970;margin-top:4px}.price-input{width:148px;padding:10px 11px;border:1px solid #ded8d0;border-radius:9px}.save-cell{width:160px}.save-state{display:block;font-size:12px;min-height:16px;margin-top:5px;color:#777}.save-state.ok{color:#16814a}.save-state.bad{color:#b42318}
        .notice{margin:16px 0;padding:12px 14px;border-radius:10px;background:#f8f1e9;color:#68503d;font-size:13px;line-height:1.5}.error{color:#b42318;font-size:13px;margin-top:12px;min-height:18px}
        @media(max-width:600px){.wrap{padding:20px 14px}.hero{display:block}.hero .sub{max-width:36ch}.tools{padding:12px}.count{font-size:12px}.lock{margin:9vh auto;padding:22px}.lock form{display:grid}.lock .button{width:100%}}
    </style>
</head>
<body>
@unless($unlocked)
    <main class="wrap">
        <div class="brand">Gownsea · Owner pricing</div>
        <section class="panel lock">
            <h1>Price list access</h1>
            <p class="sub">Enter the six-digit PIN shared with you to view and update product prices.</p>
            <form id="unlock-form">
                <input class="pin" name="pin" type="password" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="••••••" aria-label="Six digit PIN" required>
                <button class="button" type="submit">Continue</button>
            </form>
            <div class="error" id="unlock-error" role="alert"></div>
        </section>
    </main>
    <script>
        document.getElementById('unlock-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;
            const button = form.querySelector('button');
            const error = document.getElementById('unlock-error');
            button.disabled = true; button.textContent = 'Checking…'; error.textContent = '';
            try {
                const response = await fetch(@json(route('price-list.unlock')), {
                    method: 'POST', headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                    body: JSON.stringify({pin: form.elements.pin.value})
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Could not unlock the price list.');
                window.location.reload();
            } catch (err) { error.textContent = err.message || 'Connection problem. Please try again.'; }
            finally { button.disabled = false; button.textContent = 'Continue'; }
        });
    </script>
@else
    <main class="wrap">
        <div class="brand">Gownsea · Owner pricing</div>
        <header class="hero">
            <div><h1>Product price list</h1><p class="sub">Update hire and purchase prices. Changes save instantly and apply to the catalogue.</p></div>
        </header>
        <div class="notice">Enter prices in KES. Leave a field empty if that product is unavailable for that option. Existing promotional prices are managed separately.</div>
        <section class="panel">
            <div class="tools"><input id="product-search" class="search" type="search" placeholder="Search products or categories…" aria-label="Search products"><span class="count" id="product-count">{{ $products->count() }} products</span></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Product</th><th>For hire (KES)</th><th>For purchase (KES)</th><th></th></tr></thead>
                    <tbody id="product-rows">
                    @foreach($products as $product)
                        <tr data-search="{{ strtolower($product->name.' '.($product->category?->name ?? '')) }}">
                            <td><span class="product">{{ $product->name }}</span><span class="category">{{ $product->category?->name ?? 'Uncategorised' }}</span></td>
                            <td><input class="price-input" type="number" min="0" step="1" inputmode="numeric" aria-label="Hire price for {{ $product->name }}" value="{{ $product->hire_price_amount ?? '' }}" data-hire></td>
                            <td><input class="price-input" type="number" min="0" step="1" inputmode="numeric" aria-label="Purchase price for {{ $product->name }}" value="{{ $product->price_amount ?? '' }}" data-purchase></td>
                            <td class="save-cell"><button class="button save-product" type="button" data-url="{{ route('price-list.update', $product) }}">Save prices</button><span class="save-state" aria-live="polite"></span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        document.querySelectorAll('.save-product').forEach(button => button.addEventListener('click', async () => {
            const row = button.closest('tr'); const state = row.querySelector('.save-state');
            const value = selector => { const raw = row.querySelector(selector).value.trim(); return raw === '' ? null : Number(raw); };
            button.disabled = true; button.textContent = 'Saving…'; state.className = 'save-state'; state.textContent = 'Saving changes';
            try {
                const response = await fetch(button.dataset.url, {
                    method: 'PATCH', headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
                    body: JSON.stringify({hire_price:value('[data-hire]'), purchase_price:value('[data-purchase]')})
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Could not save these prices.');
                state.className = 'save-state ok'; state.textContent = '✓ Saved successfully'; button.textContent = 'Saved';
                window.setTimeout(() => { if (button.isConnected) button.textContent = 'Save prices'; }, 1800);
            } catch (err) { state.className = 'save-state bad'; state.textContent = '✕ ' + (err.message || 'Save failed. Check your connection.'); button.textContent = 'Try again'; }
            finally { button.disabled = false; }
        }));
        const search = document.getElementById('product-search');
        search.addEventListener('input', () => {
            const query = search.value.trim().toLowerCase(); let shown = 0;
            document.querySelectorAll('#product-rows tr').forEach(row => { const match = row.dataset.search.includes(query); row.hidden = !match; if (match) shown++; });
            document.getElementById('product-count').textContent = `${shown} product${shown === 1 ? '' : 's'}`;
        });
    </script>
@endunless
</body>
</html>
