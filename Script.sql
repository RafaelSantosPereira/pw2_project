CREATE DATABASE IF NOT EXISTS `u506280443_rafperDB`


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    followers_count INT DEFAULT 0,
    following_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE likes (
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- CHAVE PRIMÁRIA COMPOSTA: Impede que o mesmo user dê like 2x no mesmo post
    PRIMARY KEY (user_id, post_id), 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE followers (
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE profiles (
    user_id INT NOT NULL PRIMARY KEY,
    bio TEXT,                         
    website VARCHAR(255),
    location VARCHAR(100),            
    birthdate DATE,
    avatar_url VARCHAR(255), 
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- DELIMITER $$

-- Cria o trigger para ser executado APÓS a inserção em 'likes'
CREATE TRIGGER increment_likes_count
AFTER INSERT ON likes
FOR EACH ROW
BEGIN
    -- O comando de UPDATE incrementa o campo likes_count na tabela posts
    -- NEW.post_id se refere ao post_id que acabou de ser inserido na tabela 'likes'
    UPDATE posts
    SET likes_count = likes_count + 1
    WHERE post_id = NEW.post_id;
END;
-- $$

-- DELIMITER $$

-- Cria o trigger para ser executado APÓS a eliminação em 'likes'
CREATE TRIGGER decrement_likes_count
AFTER DELETE ON likes
FOR EACH ROW
BEGIN
    -- O comando de UPDATE decrementa o campo likes_count na tabela posts
    -- OLD.post_id se refere ao post_id que acabou de ser eliminado da tabela 'likes'
    UPDATE posts
    SET likes_count = GREATEST(0, likes_count - 1)
    WHERE post_id = OLD.post_id;
END;
-- $$

-- DELIMITER $$

-- Cria o trigger para ser executado APÓS a inserção em 'comments'
CREATE TRIGGER increment_comments_count
AFTER INSERT ON comments
FOR EACH ROW
BEGIN
    -- O comando de UPDATE incrementa o campo comments_count na tabela posts
    -- NEW.post_id se refere ao post_id que acabou de ser inserido na tabela 'comments'
    UPDATE posts
    SET comments_count = comments_count + 1
    WHERE post_id = NEW.post_id;
END;
-- $$

-- DELIMITER $$

-- Cria o trigger para ser executado APÓS a eliminação em 'comments'
CREATE TRIGGER decrement_comments_count
AFTER DELETE ON comments
FOR EACH ROW
BEGIN
    -- O comando de UPDATE decrementa o campo comments_count na tabela posts
    -- OLD.post_id se refere ao post_id que acabou de ser eliminado da tabela 'comments'
    UPDATE posts
    SET comments_count = GREATEST(0, comments_count - 1)
    WHERE post_id = OLD.post_id;
END;
-- $$

-- DELIMITER $$

CREATE TRIGGER increment_follow_counts
AFTER INSERT ON followers
FOR EACH ROW
BEGIN
    UPDATE users SET following_count = following_count + 1 WHERE id = NEW.follower_id;
    UPDATE users SET followers_count = followers_count + 1 WHERE id = NEW.following_id;
END;
-- $$

-- DELIMITER $$

CREATE TRIGGER decrement_follow_counts
AFTER DELETE ON followers
FOR EACH ROW
BEGIN
    UPDATE users SET following_count = GREATEST(0, following_count - 1) WHERE id = OLD.follower_id;
    UPDATE users SET followers_count = GREATEST(0, followers_count - 1) WHERE id = OLD.following_id;
END;
-- $$



DELIMITER $$

CREATE TRIGGER create_profile_after_signup
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO profiles (user_id) VALUES (NEW.id);
end$$

DELIMITER ;
-- DELIMITER ;