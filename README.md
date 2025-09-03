# 画像投稿掲示板アプリ (課題)

このリポジトリは、PHP + MySQL で作成した画像投稿掲示板です。  
AWS EC2 上で Docker と Docker Compose を利用して簡単に起動できます

---

## 前提条件

- AWS アカウント
- EC2 インスタンス
- PowerShell
- Git
- Docker & Docker Compose

---

# EC2環境構築 & Docker / Git / MySQLセットアップ

**対象:** Amazon Linux 2023 (kernel-6.1)  
このREADMEは、EC2インスタンスの作成から、Docker・Docker Compose・Git・MySQLテーブル作成までの手順をまとめています。

---

## 1. EC2インスタンスの作成

```bash
# 1-1. AWSマネジメントコンソールにログイン
# 1-2. EC2サービスを選択
# 1-3. 左メニューで「インスタンス → インスタンスを起動」をクリック
# 1-4. 以下を設定
#      - AMI: Amazon Linux 2023 AMI (kernel-6.1)
#      - インスタンスタイプ: t2.micro（無料利用枠対応）
#      - キーペア: 新規作成し .pem ファイルをダウンロード（必ず保存）
#      - ネットワーク設定: デフォルトのままでOK
#      - セキュリティグループ: SSH (TCP 22) を許可（自分のIPのみ）
# 1-5. 起動をクリック


# 2-1. インスタンス一覧から対象を選択
# 2-2. パブリックIPv4アドレスを控える（例: 3.12.34.56）


# 3-1. ダウンロードした .pem ファイルを指定してSSH接続
ssh -i "C:\path\to\your-key.pem" ec2-user@<パブリックIPv4アドレス>

# 4-1. Dockerをインストール
sudo yum install -y docker

# 4-2. Dockerサービスを起動
sudo systemctl start docker

# 4-3. 起動時に自動開始
sudo systemctl enable docker

# 4-4. ec2-userをdockerグループに追加
sudo usermod -a -G docker ec2-user


# 5-1. 保存先ディレクトリを作成
sudo mkdir -p /usr/local/lib/docker/cli-plugins/

# 5-2. Docker Composeをダウンロード（v2.36.0）
sudo curl -SL https://github.com/docker/compose/releases/download/v2.36.0/docker-compose-linux-x86_64 \
  -o /usr/local/lib/docker/cli-plugins/docker-compose

# 5-3. 実行権限を付与
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose


# 6-1. Gitをインストール
sudo dnf install -y git

# 6-2. GitHubリポジトリをクローン
git clone <GitHubリポジトリURL>


-- 7-1. MySQLにログイン
mysql -u <ユーザー名> -p

-- 7-2. 掲示板用テーブルを作成
CREATE TABLE `bbs_entries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,  -- 投稿ID（自動採番）
    `body` TEXT NOT NULL,                                   -- 投稿本文（必須）
    `image_filename` TEXT DEFAULT NULL,                     -- 添付画像のファイル名（任意）
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP        -- 作成日時（自動付与）
);
