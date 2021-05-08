# WiiU_3DS_ScreenShotUploadWebPage
WiiU及び3DSでWebブラウザ経由で画像ファイル等(主にスクリーンショット)を保存するための小さなWebページです。
使用するには別途PHP5.3以降が動作するhttpサーバが必要です。  

## 使い方
1. PHP5.3以降が動作するウェブサーバを用意します。PHP5.3以降さえ動作するならdockerでもncでもお好みで。Androidでも使えるhttpサーバあるらしいけど未テスト。  
2. ドキュメントルートかその配下のお好みのディレクトリに"wiiu3dsuploader.php"を置きます。  
3. 自動で保存先ディレクトリを作らせる場合、"wiiu3dsuploader.php"を置いたディレクトリの書き込み権限を許可しておきます。  
   保存先ルートディレクトリ(デフォルトでは"wiiu3dsuploader.php"を置いたディレクトリ/picture")を先に作っておき、そちらに書き込み権限を付与してもOKです。  
4. WiiUや3DSでアクセスし、ファイルをアップロードします。  

### WiiUからスクリーンショットを送信する場合
ゲームを起動中にスクリーンショットを撮影したいタイミングでHOMEボタンを押してHOMEメニューを開きます。

ゲームを起動したままWebブラウザを開いてWebページにアクセスしてファイル選択ボタンを押すと、
起動していたゲームのその時点においてのTVとゲームパッドどちらかのスクリーンショットを選択する画面になります。
WiiU側の制限により、起動中のゲームのスクリーンショット以外については取り扱うことはできません。
また、ゲームソフトによってはスクリーンショットの撮影が制限されている場合があります。

### 3DSの場合から画像ファイルを送信する場合
画像ファイルは"ニンテンドー3DSカメラ"のファイルビューアで表示可能なもののみアップロード可能となります。
このためWiiUとは違い、画像ファイルは事前にHOME画面かカメラアプリケーション、ゲーム内機能等で生成しておく必要があります。
ゲーム内機能でスクリーンショットが撮影できないものについては利用できません。

WebブラウザからWebページにアクセスしてファイル選択ボタンを押すと、
"ニンテンドー3DSカメラ"のものと同じファイルビューアが起動するのでアップロードしたい画像ファイルを選択します。
ただし、3DSのWebブラウザはデフォルトではフィルタリングによりファイルのアップロード機能がロックされています。
解除する場合、初回のみクレジットカードの利用と33円(30円+税)分のフィルタリング解除手数料を支払う必要があります。
また、3DSでは動画ファイルはAVI形式で保存されていますが、アップロード時には3DSの機能によってMKV形式(Matroska Video File)に自動的に変換されます。
これらは全て3DS側の仕様による制限であり、サーバ側の設定では回避できません。

動画ファイルは基本的にサイズが大きくなるため、php.ini等で設定を行っていないとアップロード可能ファイルサイズ上限に抵触してしまいアップロードできません。
これらの理由のため、3DSの場合は結局、SDカードの抜き差しが手間でないならばSDカードを使ってデータのやりとりを行ったほうが楽な可能性が高いです。
3DSの"ニンテンドー3DSカメラ"のファイルビューアで参照されるファイルは、SDカード内のDCIMフォルダ内にあります。

## 仕様
以下のうち、一部は設定で変更が可能です。  
WiiU及び3DSのみをターゲットにしているため、初期設定ではMIMEタイプが"image/jpeg"又は"video/x-matroska"以外のものはアップロードを受け付けない仕様です。  
ファイルは、日付日時_ランダムバイトコード_元ファイル名の名前にリネームされて保存されます。ランダムバイトコードは2桁単位で桁数の指定が可能です(初期設定では3(6桁))。  
保存先ディレクトリが無かった場合、勝手に読み書き実行全ての権限を付与された状態で作成しようとします。既にある場合でも勝手に読み書き実行全ての権限を付与しようとします。  
JPEGファイルがアップロードされた場合、解像度の情報から元となったハードウェア(WiiU又は3DS)とその取得元(TVかゲームパッドか、  
上画面か下画面かカメラでとった写真かゲームメモか)を判定し、自動的に分類を試み、分類に応じて別の場所に保存します。  
判定できない場合(Webブラウザでダウンロードした画像や別途SDカードに入れた画像等をアップロードしようとした場合とか)は判別不能の場合用のフォルダに入れられます。  
