<?php

class BookmarkApp
{
    private $db;

    public function __construct($host, $dbname, $username, $password)
    {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("データベース接続エラー: " . $e->getMessage());
        }
    }

    public function addBookmark($title, $url)
    {
        $stmt = $this->db->prepare("INSERT INTO bookmarks (title, url) VALUES (:title, :url)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':url', $url);
        $stmt->execute();
    }

    public function getBookmarks()
    {
        $stmt = $this->db->query("SELECT * FROM bookmarks");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBookmark($id)
    {
        $stmt = $this->db->prepare("DELETE FROM bookmarks WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}

// データベース接続情報
$host = 'localhost';
$dbname = 'bookmarks_db';
$username = 'root';
$password = '';

// ブックマークアプリのインスタンスを作成
$bookmarkApp = new BookmarkApp($host, $dbname, $username, $password);

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title']) && isset($_POST['url'])) {
        $title = $_POST['title'];
        $url = $_POST['url'];
        $bookmarkApp->addBookmark($title, $url);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $bookmarkApp->deleteBookmark($id);
    }
}

// ブックマーク一覧を取得
$bookmarks = $bookmarkApp->getBookmarks();
?>

<!DOCTYPE html>
<html>

<head>
    <title>ブックマークアプリ</title>
</head>

<body>
    <h1>ブックマークアプリ</h1>
    <form method="post" action="">
        <label>タイトル:</label>
        <input type="text" name="title" required>
        <label>URL:</label>
        <input type="text" name="url" required>
        <button type="submit">追加</button>
    </form>

    <h2>ブックマーク一覧</h2>
    <ul>
        <?php foreach ($bookmarks as $bookmark) : ?>
            <li>
                <a href="<?php echo $bookmark['url']; ?>" target="_blank">
                    <?php echo $bookmark['title']; ?>
                </a>
                <form method="post" action="">
                    <input type="hidden" name="delete" value="<?php echo $bookmark['id']; ?>">
                    <button type="submit">削除</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>