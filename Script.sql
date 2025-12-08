CREATE DATABASE IF NOT EXISTS `u506280443_rafperDB`


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
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
-- DELIMITER ;