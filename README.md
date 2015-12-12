# moderate-frameowrk
 Slimを機能拡張して「そこそこ」に太らせたFramework  
 PHPでサクっとWebアプリケーションを作る必要があっても、Slimは非常に便利だけど、テンプレートエンジン使えないし、デフォルトではDB接続用のモジュールもない。  
 もう少し開発を楽にしたいので、そこそこにSlimを太らせてみた。  
 あと少しだけEthnaのロジックをまねている。

# インストール
 ファイルをWebサーバに展開してcompsoerを実行。  
 `php composer.phar install --no-dev`

# インストールモジュールとバージョン

    "php": "~5.6.7",
    "slim/slim": "~2.6.2",
    "slim/extras": "2.0.*",
    "j4mie/idiorm": "1.*",
    "twig/twig": "1.*",
    "twig/extensions": "1.*",
    "smarty/smarty": "~3.1.27",
    "illuminate/session": "~5.1.22",
    "illuminate/filesystem": "~5.1.22",
    "illuminate/validation": "~5.1.22",
    "Illuminate/translation": "~5.1.22",
    "j4mie/paris": "~1.5.4"

 * PHPのバージョンは5.6以降。
 * DBアクセスにはORMのIdiormとParisを採用
 * セッション管理およびValidationにはLaravelのilluminateを採用。
 * テンプレートエンジンはTwig/Smarty3を切り替えて使用可能。(デフォルトTwig)

# パッケージ構成

    ├─app
    │  ├─Controllers
    │  ├─Models
    │  ├─Utils
    │  └─Validations
    ├─config
    ├─lib
    ├─sql
    ├─templates
    ├─tests
    └─web
      ├─css
      ├─img
      └─js

|ディレクトリ|概要|備考|
|---|---|---|
|app|各PHPファイルを格納||
| Controllers|SlimのRoutingファイルを格納||
| Models|ORMを使ったモデルや、データクラスを作成する場合に使用||
| Utils|SlimのRouting用Middlewareや便利ファンクションを格納||
| Validations|Validationクラスファイルを格納||
|config|コンフィグファイルはPHPファイルで作成する||
|lib|Slimの機能拡張用Wapperファイルを格納||
|sql|スキーマファイルを格納||
|templates|テンプレートファイルを格納||
|tests|テストファイルを格納||
|web|css/img/jsを格納|index.phpもここに配置|

# アプリ起動
 基本はSlimベースなので、web/index.phpを起点に起動する。  
 composer経由ではなく、個別にrequireしたいファイルがある場合はweb/index.phpに記載する。  
 Routing等Slimの機能はすべて使えるため、Slimのルールがそのまま使用可能

 一部"slim/extras"を取り込んでいる。

# ディレクトリ作成
 以下のディレクトリを作成しておくとよい(権限に注意)
    mkdir logs tmp
    mkdir tmp/sessions tmp/views

# Routing
 app/Controllersに配置する。  
 Controllers配下に[～Controller.php]という名前でファイルを作成すると、moderate-frameworkはアプリ起動時に全Controllerファイルを自動で読み込む。  
 ※Slimのチュートリアルによくある、index.phpに書いたRoutingも使用可能

# config
 configはPHPファイルとして記載する。  
 config_dev.php.distはリネームして、使用すると、環境依存のパラメータ設定に使用できる。

# Session
 Laravelのilluminate/sessionを追加実装している。  
 使用する場合はsessionsディレクトリを作成しておくこと。  
 ※config.phpでパスの調整が可能  

  Session機能の取り込みには、"yusukezzz/slim-session-manager
"を採用しようとしたが、composerの依存関係の問題で、lib/配下で独自に拡張したものを使用している。

# Validation
 Laravelのilluminate/validationを追加実装している。
 使用する場合は、MfBaseValidationクラスを継承したファイルを作成すること。

# Manager
 moderate-frameworkはManager制を採用している。  
 Managerは[app/～Manager.php]名前でファイルを作成すると、moderate-frameworkはアプリ起動時に全Managerファイルを自動で読み込む。  

 MfBaseManagerを継承したManagerを作成するとIdiormを使ったSQLでのDBアクセスを行うことができる。
 ※この辺りはEthna2.3の頃のDBライブラリのインスパイア  

# ORM
 Idiormでの簡易ORMとParisを使ったActiveRecord的なモデルを使用可能。

# Models
 moderate-frameworkでは所謂モデルを必須としない。  
 ※Slimにそもそも存在しない。  
 ディレクトリのみ用意しているので、Validation連携＋ORMを使うもよし、ControllerとManagerだけで実装するもよし。

# Utils
 Routing用のMiddlewareとしてSession判定ファンクションとCSRF判定ファンクションを用意している。使い方はControllers配下のサンプル参照。  
 UtilsファンクションまたはUtilsクラスを追加するとより便利に使える。

# template
 テンプレートエンジンにはTwigとSmarty3を採用。  
 切り替えはconfig.phpをコメントアウトすることでおこなう。  
 Twig/Smarty側ではconfig/sessionを参照可能となるように改造している。
