<?php 


function sqn_qr_studio_shortcode_handler() {
    ob_start();
    ?>
    <div class="sqn-wp-root">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;800&family=Plus+Jakarta+Sans:wght@700;800&family=Montserrat:wght@700;900&family=Playfair+Display:wght@700;900&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
        
        <style>
            .sqn-wp-root {
                --sqn-prim: #0077b7;
                --sqn-txt: #0f172a;
                --sqn-mut: #64748b;
                all: initial; /* Reset for WP compatibility */
                font-family: 'Inter', sans-serif;
                display: flex;
                justify-content: center;
                padding: 40px 10px;
            }

            .sqn-app-box {
                width: 100%;
                border-radius: 24px;
                display: grid;
                grid-template-columns: 1fr 440px;
                overflow: hidden;
            }

            .sqn-side {
                padding: 30px;
                border-right: 1px solid #eef2f6;
                background: #fff;
                display: flex;
                flex-direction: column;
                gap: 18px;
            }

            .sqn-brand {
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 22px;
                font-weight: 800;
                color: var(--sqn-prim);
                margin-bottom: 5px;
            }

            .sqn-tag { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--sqn-mut); letter-spacing: 1px; display: block; margin-bottom: 5px;}

            .sqn-field {
                width: 100% !important;
                padding: 12px !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 10px !important;
                font-size: 14px !important;
                margin-bottom: 5px !important;
            }

            .sqn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

            .sqn-preview {
                background: #f8fafc;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 40px;
            }

            /* --- THE QR CARD --- */
            #sqn-printable-card {
                background: white;
                padding: 45px;
                border-radius: 35px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                text-align: center;
                width: 320px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            /* FIX: Shape Clipping */
            .sqn-qr-frame {
                position: relative;
                background: white;
                line-height: 0;
                overflow: hidden; 
                display: inline-block;
            }

            #sqn-qr-code canvas, #sqn-qr-code img {
                width: 210px !important;
                height: 210px !important;
            }

            .sqn-logo-overlay {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                border-radius: 8px;
                padding: 4px;
                box-shadow: 0 3px 10px rgba(0,0,0,0.1);
                z-index: 5;
                display: none;
            }

            .sqn-logo-overlay img { width: 100%; height: 100%; object-fit: contain; }

            .sqn-name-view {
                font-size: 26px;
                font-weight: 900;
                margin-top: 25px;
                line-height: 1.1;
            }

            .sqn-hint-view {
                font-size: 11px;
                font-weight: 700;
                color: var(--sqn-mut);
                margin-top: 10px;
                letter-spacing: 2px;
            }

            .sqn-btns { display: flex; gap: 10px; width: 100%; max-width: 320px; margin-top: 25px; }

            .sqn-btn {
                flex: 1;
                padding: 14px;
                border-radius: 12px;
                font-weight: 700;
                cursor: pointer;
                border: none;
                font-size: 13px;
                transition: 0.2s;
            }

            .sqn-btn-p { background: var(--sqn-txt); color: white; }
            .sqn-btn-s { background: white; border: 1px solid #ddd; color: var(--sqn-txt); }

            /* --- UNIVERSAL PRINT FIX --- */
            @media print {
                body * { visibility: hidden; }
                #sqn-printable-card, #sqn-printable-card * { visibility: visible; }
                #sqn-printable-card {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    box-shadow: none !important;
                    border: 1px solid #eee !important;
                }
            }

            @media (max-width: 850px) {
                .sqn-app-box { grid-template-columns: 1fr; }
                .sqn-side { border-right: none; border-bottom: 1px solid #eee; }
            }
        </style>

        <div class="sqn-app-box">
            <div class="sqn-side">
                <div class="sqn-brand">QR Studio Pro</div>
                
                <div>
                    <span class="sqn-tag">1. Store Details</span>
                    <input type="text" id="sqnInName" class="sqn-field" placeholder="Store Name" oninput="sqnDraw()" value="MY BOUTIQUE">
                    <input type="text" id="sqnInLink" class="sqn-field" placeholder="UPI or URL" oninput="sqnDraw()" value="https://google.com">
                </div>

                <div class="sqn-row">
                    <div>
                        <span class="sqn-tag">Color</span>
                        <input type="color" id="sqnInCol" value="#0077b7" class="sqn-field" style="height:45px; padding:2px" oninput="sqnDraw()">
                    </div>
                    <div>
                        <span class="sqn-tag">Font Variation</span>
                        <select id="sqnInFont" class="sqn-field" onchange="sqnDraw()">
                            <option value="'Plus Jakarta Sans'">Jakarta</option>
                            <option value="'Montserrat'">Montserrat</option>
                            <option value="'Playfair Display'">Playfair</option>
                            <option value="'Oswald'">Oswald</option>
                            <option value="'Inter'">Inter</option>
                        </select>
                    </div>
                </div>

                <div class="sqn-row">
                    <div>
                        <span class="sqn-tag">QR Shape</span>
                        <select id="sqnInShape" class="sqn-field" onchange="sqnDraw()">
                            <option value="0px">Square</option>
                            <option value="25px">Rounded</option>
                            <option value="50px">Extra Round</option>
                            <option value="110px">Circle</option>
                        </select>
                    </div>
                    <div>
                        <span class="sqn-tag">Padding</span>
                        <input type="number" id="sqnInPad" class="sqn-field" value="10" oninput="sqnDraw()">
                    </div>
                </div>

                <div>
                    <span class="sqn-tag">2. Center Logo</span>
                    <div class="sqn-btn-s sqn-btn" style="text-align:center" onclick="document.getElementById('sqnFile').click()">Choose Image</div>
                    <input type="file" id="sqnFile" hidden accept="image/*" onchange="sqnUp(this)">
                    <div style="margin-top:10px">
                        <span class="sqn-tag">Logo Size</span>
                        <input type="range" id="sqnInSize" min="30" max="80" value="50" oninput="sqnDraw()" style="width:100%">
                    </div>
                </div>

                <div>
                    <span class="sqn-tag">3. Caption</span>
                    <input type="text" id="sqnInHint" class="sqn-field" placeholder="Scan to Pay" oninput="sqnDraw()" value="SCAN TO PAY">
                </div>
            </div>

            <div class="sqn-preview">
                <div id="sqn-printable-card">
                    <div class="sqn-qr-frame" id="sqn-frame">
                        <div id="sqn-qr-code"></div>
                        <div class="sqn-logo-overlay" id="sqn-logo-box">
                            <img id="sqn-logo-img" src="">
                        </div>
                    </div>
                    <div class="sqn-name-view" id="sqn-view-name">MY BOUTIQUE</div>
                    <div class="sqn-hint-view" id="sqn-view-hint">SCAN TO PAY</div>
                </div>

                <div class="sqn-btns">
                    <button class="sqn-btn sqn-btn-s" onclick="window.print()">Print</button>
                    <button class="sqn-btn sqn-btn-p" onclick="sqnDl()">Download</button>
                </div>
            </div>
        </div>

        <script>
            let sqnImg = null;

            function sqnUp(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => { sqnImg = e.target.result; sqnDraw(); };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function sqnDraw() {
                const name = document.getElementById('sqnInName').value || "SHOP";
                const link = document.getElementById('sqnInLink').value || " ";
                const col = document.getElementById('sqnInCol').value;
                const font = document.getElementById('sqnInFont').value;
                const hint = document.getElementById('sqnInHint').value;
                const shape = document.getElementById('sqnInShape').value;
                const pad = document.getElementById('sqnInPad').value;
                const size = document.getElementById('sqnInSize').value;

                const qrc = document.getElementById('sqn-qr-code');
                qrc.innerHTML = '';
                new QRCode(qrc, { text: link, width: 210, height: 210, colorDark: col, colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.H });

                const frame = document.getElementById('sqn-frame');
                frame.style.borderRadius = shape;
                frame.style.padding = pad + 'px';

                const box = document.getElementById('sqn-logo-box');
                if(sqnImg) {
                    box.style.display = 'block';
                    box.style.width = size + 'px';
                    box.style.height = size + 'px';
                    document.getElementById('sqn-logo-img').src = sqnImg;
                }

                const nv = document.getElementById('sqn-view-name');
                nv.innerText = name.toUpperCase();
                nv.style.color = col;
                nv.style.fontFamily = font;
                document.getElementById('sqn-view-hint').innerText = hint.toUpperCase();
            }

            async function sqnDl() {
                const card = document.getElementById('sqn-printable-card');
                const canvas = await html2canvas(card, { scale: 3, useCORS: true, backgroundColor: "#ffffff" });
                const link = document.createElement('a');
                link.download = 'StoreQR.png';
                link.href = canvas.toDataURL();
                link.click();
            }

            window.addEventListener('load', sqnDraw);
        </script>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('sqn_qr_studio', 'sqn_qr_studio_shortcode_handler');