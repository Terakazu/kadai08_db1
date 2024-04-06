<?php
//DB接続
// function db_conn()
// {

        try {
            $db_name =  '*********';            //データベース名
            $db_host =  '*********';  //DBホスト
            $db_id =    '*********';                //アカウント名(登録しているドメイン)
            $db_pw =    '**********';   
        
        $server_info ='mysql:dbname='.$db_name.';charset=utf8;host='.$db_host;
        $pdo = new PDO($server_info, $db_id, $db_pw);
        // return $pdo;

    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
  

// try {
//   //Password:MAMP='root',XAMPP=''
//     $pdo = new PDO('mysql:dbname=jd_db;charset=utf8;host=localhost','root','');
//   } catch (PDOException $e) {
//     exit('DB_CONECT:'.$e->getMessage());
//   }


//２．データ登録SQL作成
$sql="SELECT * FROM jd_an_table ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

//３．データ表示
// $view="";
if($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();  
  exit("SQL_ERROR:".$error[2]);
}



//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
//JSONに値を渡す場合に使う
$json = json_encode($values,JSON_UNESCAPED_UNICODE);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<title>Journaling Daiary読み込み</title>
<link rel="stylesheet" href="css/sample2.css">

</head>

<body>
<!-- Head[Start] -->
<header>
        <h1><i>Journaling Diary</i></h1>
    </header>
<!-- Head[End] -->

<!-- Main[Start] -->
<h3>感情の変化</h3>
<canvas id="myChart"></canvas>

<h3>日記</h3>
<div id="cardContainer">

 
</div>
<!-- Main[End]  -->

<script>
  //JSON受け取り
const obj = <?=$json?>;
console.log(obj);

// 日付をフォーマットする関数
function formatDate(dateString) {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = date.getMonth() + 1;
    const day = date.getDate();
    const dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'][date.getDay()];
    return `${year}年${month}月${day}日(${dayOfWeek})`;
}

// カードを生成する関数
function createCard(data) {
    const cardContainer = document.createElement('div'); // cardContainerを作成
    cardContainer.classList.add('cardContainer'); // cardcontainerクラスを追加
    
    // 日付を指定された形式にフォーマットして表示
    const formattedDate = formatDate(data.date);

    // すでに表示された日付と重複していない場合にのみ追加する
    if (!document.querySelector(`.cardContainer .date[data-date="${data.date}"]`)) {
        cardContainer.innerHTML += `<div class="date" data-date="${data.date}">${formattedDate}</div>`;
    }

    const card = document.createElement('div');
    card.classList.add('card');
    
    // 感情をカンマで分割して配列に変換
    const emotionsArray = data.emotion.split('、');
    
    // 感情を一つずつ表示するためのHTMLを生成
    const emotionsHTML = emotionsArray.map(emotion => `<div class="emotion">${emotion}</div>`).join('');
    
   let imageHTML = ''; // 画像要素を格納する変数を定義

    if (data.image_path) {
        imageHTML = `<img src="${data.image_path}">`; // 画像パスがある場合のみ画像要素を生成
    }
    
    card.innerHTML = `
        <div class="card-content">
            <div class="memo">${data.memo}</div>
            <div class="date">${formattedDate}</div> <!-- 元のままのdate -->
            <div class="emotion-wrapper">${emotionsHTML}</div> <!-- 感情を囲むラッパー -->
        </div>
        ${imageHTML} <!-- 画像要素の条件分岐 -->
    `;
    
    cardContainer.appendChild(card); // cardをcardcontainerに追加
    
    return cardContainer; // cardcontainerを返す
}


  // カードを表示する
  const cardContainer = document.getElementById('cardContainer');
  obj.forEach(data => {
    const card = createCard(data);
    cardContainer.appendChild(card);
  });

  
  const selectedEmotions =[];
  const dates=[];
  let totalScore = 0;


  //感情のスコア
// 1.各感情のスコアを定義する
const emotionScores = {
    "満足": 5,
    "感謝": 5,
    "嬉しい": 5,
    "ワクワク":5,
    "好き":3,
    "感心":3,
    "面白い":3,
    "楽しい":3,
    "すっきり":1,
    "ドキドキ":1,
    "安心":1,
    "穏やか":1,
    "普通":-1,
    "退屈":-1,
    "もやもや":-1,
    "緊張":-1,
    "不安":-3,
    "悲しい":-3,
    "疲れた":-3,
    "イライラ":-3,
};


// 選択された感情から合計スコアを計算する関数
function calculateTotalScore(selectedEmotions) {
    let totalScore = 0;
    selectedEmotions.forEach(emotion => {
        totalScore += emotionScores[emotion];
    });
    return totalScore;
}

// 各カードの感情を配列に変換して合計スコアを計算する
obj.forEach(item => {
    const emotionsArray = item.emotion.split('、'); // カンマで区切って配列に変換
    const score = calculateTotalScore(emotionsArray); // 合計スコアを計算
    console.log('カードの感情:', emotionsArray);
    console.log('合計スコア:', score);
});

// 各カードの感情を配列に変換して合計スコアを計算する
const scoresByDate = {}; // 日付ごとのスコアを格納するオブジェクト

obj.forEach(item => {
    const emotionsArray = item.emotion.split('、'); // カンマで区切って配列に変換
    const score = calculateTotalScore(emotionsArray); // 合計スコアを計算

    // 日付ごとのスコアをオブジェクトに追加
    if (!scoresByDate[item.date]) {
        scoresByDate[item.date] = score;
    } else {
        scoresByDate[item.date] += score;
    }
});

// 日付ごとの最大スコアを計算する関数
function calculateMaxScoresByDate(data) {
    const maxScoresByDate = {};
    data.forEach(item => {
        const date = item.date;
        const emotionArray = item.emotion.split('、');
        const totalScore = emotionArray.reduce((acc, emotion) => acc + emotionScores[emotion], 0);
        if (!maxScoresByDate[date] || maxScoresByDate[date] < totalScore) {
            maxScoresByDate[date] = totalScore;
        }
    });

    return maxScoresByDate;
}

// 日付ごとの最大スコアを取得
const maxScoresByDate = calculateMaxScoresByDate(obj);
console.log(maxScoresByDate);

// グラフ作成のためのデータ配列を初期化
const graphData = [];

// グラフデータの作成
for (const date in maxScoresByDate) {
    if (Object.hasOwnProperty.call(maxScoresByDate, date)) {
        graphData.push({ date: date, score: maxScoresByDate[date] });
    }
}

// グラフデータを日付でソート
graphData.sort((a, b) => new Date(a.date) - new Date(b.date));

// グラフ用のデータを準備
const dates2 = Object.keys(scoresByDate).reverse(); // 日付の配列を逆順にする
const maxScores = dates2.map(date => maxScoresByDate[date]); // 逆順になった日付に対応する最大スコアを取得

// Chart.jsを使用して折れ線グラフを描画
const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates2, // 逆順の日付
        datasets: [{
            label: '感情スコア',
            data: maxScores, // 逆順の最大スコア
            borderColor: 'rgba(255, 99, 132)',
            backgroundColor:['rgba(255, 99, 132, 0.2)'],
            borderWidth: 2,
            fill: true, // グラフの下側を塗りつぶす
            lineTension: 0.4 // 曲線の滑らかさを設定
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

</script>
</body>
</html>
 