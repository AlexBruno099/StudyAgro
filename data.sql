-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS studyagro;
USE studyagro;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de tarefas
CREATE TABLE IF NOT EXISTS tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50),
    prioridade ENUM('Baixa','Média','Alta') DEFAULT 'Média',
    status ENUM('Pendente','Em andamento','Concluída') DEFAULT 'Pendente',
    data_inicio DATE,
    data_fim DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de materiais
CREATE TABLE IF NOT EXISTS materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    tipo ENUM('Livro','Artigo','PDF','Vídeo') DEFAULT 'Livro',
    categoria VARCHAR(50),
    status ENUM('Não iniciado','Lendo','Concluído') DEFAULT 'Não iniciado',
    link VARCHAR(255),
    arquivo VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de checklists
CREATE TABLE IF NOT EXISTS checklists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tarefa_id INT NOT NULL,
    concluido BOOLEAN DEFAULT FALSE,
    data_conclusao DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tarefa_id) REFERENCES tarefas(id) ON DELETE CASCADE
);
