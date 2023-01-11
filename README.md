# ocrapi-rpa
RPA sample programs using OCR API

# Demo

<a href="https://app.softwarenote.info/ocr_rpa/" target="_blank">https://app.softwarenote.info/ocr_rpa/</a>

# Documents

OCR API を利用した RPA【Robotic Process Automation】のサンプルプログラムです。<br>
例えば見積書や請求書などの同じフォーマットの画像を API 呼び出しやキャッシュで読み込みながら、画像の違いや OCR サービスの違いでテキストの読み取り具合を比較できるような機能を作ります。<br>
<br>
・あらかじめ定められた座標（発行日、請求先、見積金額）にあるテキストを取得する<br>
・画像をマウスでドラッグすることにより設定座標を変更できる<br>
・変更された座標はセッションに保持し、画面遷移があっても引き継ぐ<br>
・API 呼び出しやキャッシュ読み込みは前回のサンプル同様の機能を持つ<br>
<br>
呼び出せる API は Google Cloud Vision API と Microsoft Computer Vision API になります。<br>
<br>
samples/ocr_api をドキュメントルート上に展開し、defines.incの以下の define 値を設定すると動くかと思います。<br>
キャッシュディレクトリのアクセス権に注意してください。<br>
<br>
**OCR_GOOGLE_APIKEY**： Google の API Key<br>
**OCR_MICROSOFT_APIKEY**： Microsoft の API Key<br>
**OCR_MICROSOFT_CUSTOMDOMAIN**： Microsoft のカスタムドメイン<br>
**OCR_CACHE_PATH**： キャッシュデータを保存するディレクトリ<br>
<br>
<br>
詳細はこちら<br>
<a href="https://softwarenote.info/p3346/" target="_blank">https://softwarenote.info/p3346/</a>
<br>
Demo & Tutorial<br>
<a href="https://app.softwarenote.info/ocr_rpa/" target="_blank">https://app.softwarenote.info/ocr_rpa/</a>
<br>
