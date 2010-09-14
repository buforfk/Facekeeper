<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Youtube 編碼工具</title>
</head>
<body>
    <h1>Youtube 編碼工具</h1>
    <form action="youtube.php" method="post">
    <?php
        if($_POST['text'])
        {
            file_put_contents('tmp/youtube_temp.txt', $_POST['text']);
            exec('python youtube.py > tmp/youtube_result.txt');
            echo '<p>已編碼完成，請到 <a href="tmp/youtube_result.txt" target="_blank">這裡</a> 下載編碼後的文件檔，並貼到您的 RE 編輯器裡。<br /><b>(本份檔案將不會留存，會被再產生的檔案給覆蓋)</b></p><p><a href="youtube.php" style="color:green;text-decoration:none;">再產生另一組編碼文件</a></p>';
        }
        else
        {
        ?>
    <p>請把您從 Youtube 頁面抓取的原始碼貼在下面的框框中，並按下「送出並編碼」按鍵。</p><p>程式將會去除掉所有的換行字元及空白後回傳給您下載，以方便您更新 RE。</p>
    <textarea style="width:300px; height:200px;" name="text"></textarea>
    <br />
    <input type="submit" value="送出並編碼" />
<?
        }
    ?>
        </form>
</body>
</html>
