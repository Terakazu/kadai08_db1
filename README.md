# ①課題番号-プロダクト名
PHP2 - DB連携サービス（作成・参照）
Journaling Diary

## ②課題内容（どんな作品か）
-内省を促すアプリを作成中で日記と感情を書くところを作成

## ③DEMO
https://kekechama515.sakura.ne.jp/php02/index.php

## ④工夫した点・こだわった点

-感情をセレクトボックスで選択。1つ以上選ばないと送信できないようにした。（上限は3つまで）

‐感情に点数をつけてグラフを表示

‐1日に複数回投稿できるが、グラフに反映されるのは一番値が大きいもの（最大15）

## ④難しかった点・次回トライしたいこと(又は機能)

-当初、データベースで感情をNULL設定していなかったので、感情を入れないと送信できなかった。
 NULLをOKにしたら、今度はグラフが表示されなくなったので、最終的には送信するときの制約をつけた。

-難しくはないが、さくらサーバ―にあげてから修正したら、都度FileZillaで上げなおさないといけないのがとても面倒だった。

‐次回は、時間も表示したい。

## ⑤質問・疑問・感想、シェアしたいこと等なんでも

- [感想]本番環境にアップしてからも予期せぬエラーはでるものだと学んだ。
- [tips]
- [参考記事]
