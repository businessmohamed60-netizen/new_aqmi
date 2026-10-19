-- Table pour le rate limiting des tentatives d'authentification
-- Permet de détecter et bloquer les robots qui spamment les endpoints /login, /otp, /register, /forgot

CREATE TABLE IF NOT EXISTS auth_attempts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    endpoint VARCHAR(20) NOT NULL,
    result VARCHAR(20) NOT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_auth_attempts_ip (ip_address),
    INDEX idx_auth_attempts_endpoint (endpoint),
    INDEX idx_auth_attempts_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
