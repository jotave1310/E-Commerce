-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para ecommerce_db
DROP DATABASE IF EXISTS `ecommerce_db`;
CREATE DATABASE IF NOT EXISTS `ecommerce_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `ecommerce_db`;

-- Copiando estrutura para tabela ecommerce_db.avaliacoes
DROP TABLE IF EXISTS `avaliacoes`;
CREATE TABLE IF NOT EXISTS `avaliacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` >= 1 and `nota` <= 5),
  `comentario` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_avaliacao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`produto_id`,`usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_avaliacoes_produto` (`produto_id`),
  CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.avaliacoes: ~7 rows (aproximadamente)
REPLACE INTO `avaliacoes` (`id`, `produto_id`, `usuario_id`, `nota`, `comentario`, `ativo`, `data_avaliacao`) VALUES
	(1, 1, 2, 5, 'Excelente smartphone! A câmera é realmente impressionante e a S Pen é muito útil para trabalho.', 1, '2025-08-13 14:41:41'),
	(2, 2, 2, 4, 'Ótimo notebook para trabalho. Performance excelente e a tela é muito boa.', 1, '2025-08-13 14:41:41'),
	(3, 3, 2, 5, 'Melhor fone que já tive! O cancelamento de ruído é perfeito para trabalhar em casa.', 1, '2025-08-13 14:41:41'),
	(4, 4, 2, 5, 'Qualidade de imagem incrível! Vale cada centavo investido.', 1, '2025-08-13 14:41:41'),
	(5, 1, 1, 4, 'Produto de qualidade, entrega rápida. Recomendo!', 1, '2025-08-13 14:41:41'),
	(6, 3, 1, 5, 'Som excepcional e muito confortável para uso prolongado.', 1, '2025-08-13 14:41:41'),
	(7, 14, 2, 5, 'Excelente', 1, '2025-08-13 18:30:01'),
	(8, 10, 2, 1, 'Muito boa', 1, '2025-08-13 21:58:42');

-- Copiando estrutura para tabela ecommerce_db.carrinho
DROP TABLE IF EXISTS `carrinho`;
CREATE TABLE IF NOT EXISTS `carrinho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`usuario_id`,`produto_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `carrinho_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.carrinho: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela ecommerce_db.carrinhos
DROP TABLE IF EXISTS `carrinhos`;
CREATE TABLE IF NOT EXISTS `carrinhos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `carrinhos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.carrinhos: ~2 rows (aproximadamente)
REPLACE INTO `carrinhos` (`id`, `usuario_id`, `data_criacao`, `data_atualizacao`) VALUES
	(1, 1, '2025-08-13 20:24:16', '2025-08-13 20:24:16'),
	(2, 2, '2025-08-13 20:25:03', '2025-08-13 20:25:03');

-- Copiando estrutura para tabela ecommerce_db.carrinho_itens
DROP TABLE IF EXISTS `carrinho_itens`;
CREATE TABLE IF NOT EXISTS `carrinho_itens` (
  `carrinho_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`carrinho_id`,`produto_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `carrinho_itens_ibfk_1` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinhos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carrinho_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.carrinho_itens: ~2 rows (aproximadamente)
REPLACE INTO `carrinho_itens` (`carrinho_id`, `produto_id`, `quantidade`, `data_adicao`) VALUES
	(1, 13, 1, '2025-08-13 23:09:51');

-- Copiando estrutura para tabela ecommerce_db.categorias
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.categorias: ~6 rows (aproximadamente)
REPLACE INTO `categorias` (`id`, `nome`, `descricao`, `ativo`, `data_criacao`, `data_atualizacao`) VALUES
	(1, 'Eletrônicos', 'Dispositivos eletrônicos como smartphones, tablets e TVs', 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41'),
	(2, 'Informática', 'Computadores, notebooks, periféricos e acessórios', 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41'),
	(3, 'Gaming', 'Produtos para jogos e entretenimento', 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41'),
	(4, 'Áudio', 'Fones de ouvido, caixas de som e equipamentos de áudio', 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41'),
	(5, 'Casa Inteligente', 'Dispositivos para automação residencial', 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41'),
	(6, 'Acessórios', 'Cabos, carregadores e outros acessórios tecnológicos', 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41');

-- Copiando estrutura para tabela ecommerce_db.itens_pedido
DROP TABLE IF EXISTS `itens_pedido`;
CREATE TABLE IF NOT EXISTS `itens_pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.itens_pedido: ~6 rows (aproximadamente)
REPLACE INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
	(1, 3, 10, 1, 399.99),
	(2, 4, 10, 5, 399.99),
	(3, 5, 10, 1, 399.99),
	(4, 6, 10, 1, 399.99),
	(5, 7, 10, 1, 399.99),
	(6, 7, 13, 1, 799.99);

-- Copiando estrutura para tabela ecommerce_db.pedidos
DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pendente','processando','enviado','entregue','cancelado') DEFAULT 'pendente',
  `endereco_entrega` text DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pedidos_usuario` (`usuario_id`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.pedidos: ~5 rows (aproximadamente)
REPLACE INTO `pedidos` (`id`, `usuario_id`, `total`, `status`, `endereco_entrega`, `data_pedido`, `data_atualizacao`) VALUES
	(3, 2, 399.99, 'pendente', 'rua exemplo, 123', '2025-08-13 21:15:35', '2025-08-13 21:15:35'),
	(4, 2, 1999.95, 'pendente', 'Rua silva, 557', '2025-08-13 21:16:47', '2025-08-13 21:16:47'),
	(5, 2, 399.99, 'pendente', 'rua junior,887', '2025-08-13 21:27:14', '2025-08-13 21:27:14'),
	(6, 2, 399.99, 'pendente', 'rua junior,887', '2025-08-13 21:32:04', '2025-08-13 21:32:04'),
	(7, 2, 1199.98, 'pendente', 'Rua silva, 557', '2025-08-13 22:11:46', '2025-08-13 22:11:46');

-- Copiando estrutura para tabela ecommerce_db.produtos
DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao_curta` text DEFAULT NULL,
  `descricao_longa` longtext DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0,
  `especificacoes_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`especificacoes_json`)),
  `destaque` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `imagem_capa_id` int(11) DEFAULT NULL,
  `imagens_extras` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`imagens_extras`)),
  PRIMARY KEY (`id`),
  KEY `idx_produtos_categoria` (`categoria_id`),
  KEY `idx_produtos_destaque` (`destaque`),
  KEY `idx_produtos_ativo` (`ativo`),
  KEY `imagem_capa_id` (`imagem_capa_id`),
  CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`imagem_capa_id`) REFERENCES `imagens_produto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.produtos: ~14 rows (aproximadamente)
REPLACE INTO `produtos` (`id`, `nome`, `descricao_curta`, `descricao_longa`, `preco`, `categoria_id`, `estoque`, `especificacoes_json`, `destaque`, `ativo`, `data_criacao`, `data_atualizacao`, `imagem_capa_id`, `imagens_extras`) VALUES
	(1, 'Smartphone Samsung Galaxy S24 Ultra', 'Smartphone premium com tela Dynamic AMOLED de 6.8 polegadas, câmera de 200MP e S Pen integrada.', 'O Samsung Galaxy S24 Ultra representa o ápice da tecnologia móvel, oferecendo uma experiência incomparável para usuários exigentes. Com sua tela Dynamic AMOLED 2X de 6.8 polegadas e resolução QHD+, cada imagem ganha vida com cores vibrantes e detalhes impressionantes. O display suporta taxa de atualização adaptativa de 120Hz, garantindo fluidez excepcional em jogos e navegação.\n\nA câmera principal de 200MP com zoom óptico de 10x permite capturar fotos profissionais mesmo à distância, enquanto o sistema de estabilização óptica garante imagens nítidas em qualquer situação. O processador Snapdragon 8 Gen 3 oferece desempenho excepcional para multitarefas e jogos pesados, complementado por 12GB de RAM LPDDR5X e 256GB de armazenamento UFS 4.0.\n\nA S Pen integrada transforma o dispositivo em uma ferramenta de produtividade completa, permitindo anotações precisas, edição de fotos e controle remoto de apresentações. A bateria de 5000mAh com carregamento rápido de 45W garante uso intenso durante todo o dia, enquanto a certificação IP68 oferece proteção contra água e poeira.', 899.99, 1, 15, '{"Tela": "6.8\\" Dynamic AMOLED 2X, QHD+, 120Hz", "Processador": "Snapdragon 8 Gen 3", "RAM": "12GB LPDDR5X", "Armazenamento": "256GB UFS 4.0", "Câmera Principal": "200MP + 50MP + 12MP + 10MP", "Câmera Frontal": "12MP", "Bateria": "5000mAh, carregamento 45W", "Sistema": "Android 14 com One UI 6.1", "Conectividade": "5G, Wi-Fi 7, Bluetooth 5.3", "Resistência": "IP68", "Dimensões": "162.3 x 79.0 x 8.6 mm", "Peso": "232g"}', 1, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(2, 'Notebook Dell Inspiron 15 3000 Business', 'Notebook profissional com processador Intel Core i5 de 12ª geração, 16GB RAM e SSD 512GB.', 'O Dell Inspiron 15 3000 Business é a escolha ideal para profissionais que buscam performance, confiabilidade e produtividade. Equipado com processador Intel Core i5-1235U de 12ª geração, oferece o equilíbrio perfeito entre eficiência energética e poder de processamento para todas as tarefas do dia a dia.\n\nCom 16GB de RAM DDR4 expansível até 32GB, o multitasking torna-se fluido e eficiente, permitindo trabalhar com múltiplas aplicações simultaneamente sem perda de performance. O SSD NVMe de 512GB garante inicialização rápida do sistema e acesso instantâneo aos arquivos, enquanto oferece espaço suficiente para documentos, projetos e aplicações profissionais.\n\nA tela Full HD de 15.6 polegadas com tecnologia antirreflexo proporciona visualização confortável mesmo em ambientes com muita luz, ideal para longas jornadas de trabalho. A placa de vídeo Intel Iris Xe integrada oferece performance gráfica adequada para edição básica de imagens e vídeos, além de suportar múltiplos monitores externos.\n\nO design robusto e elegante, combinado com teclado numérico completo e touchpad de precisão, garante conforto e produtividade em qualquer ambiente de trabalho. A conectividade completa inclui portas USB-A, USB-C, HDMI e leitor de cartão SD.', 2499.99, 2, 8, '{"Processador": "Intel Core i5-1235U (12ª geração)", "RAM": "16GB DDR4 (expansível até 32GB)", "Armazenamento": "SSD 512GB NVMe", "Tela": "15.6\\" Full HD (1920x1080) antirreflexo", "Placa de Vídeo": "Intel Iris Xe integrada", "Sistema Operacional": "Windows 11 Pro", "Conectividade": "Wi-Fi 6, Bluetooth 5.2", "Portas": "2x USB-A 3.2, 1x USB-C, HDMI, RJ45, leitor SD", "Bateria": "3 células, 41Wh", "Peso": "1.85kg", "Dimensões": "358.5 x 235.6 x 18.9 mm", "Garantia": "1 ano"}', 1, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(3, 'Fone de Ouvido Sony WH-1000XM5 Wireless', 'Fone de ouvido premium com cancelamento de ruído líder da indústria e qualidade de áudio Hi-Res.', 'O Sony WH-1000XM5 estabelece novos padrões em cancelamento de ruído e qualidade de áudio, sendo a escolha preferida de audiophiles e profissionais ao redor do mundo. Com tecnologia de cancelamento de ruído de próxima geração, utiliza dois processadores dedicados e oito microfones para eliminar praticamente todos os ruídos externos.\n\nOs drivers de 30mm especialmente desenvolvidos oferecem resposta de frequência excepcional, reproduzindo cada detalhe musical com clareza cristalina. A tecnologia LDAC garante transmissão de áudio em alta resolução via Bluetooth, mantendo a qualidade próxima ao CD mesmo sem fio.\n\nA bateria de longa duração oferece até 30 horas de reprodução com cancelamento de ruído ativado, e o carregamento rápido de apenas 3 minutos proporciona 3 horas adicionais de uso. Os controles touch intuitivos permitem ajustar volume, pular faixas e ativar assistentes de voz com gestos simples.\n\nO design ergonômico com almofadas macias garante conforto durante uso prolongado, enquanto a construção dobrável facilita o transporte. A tecnologia Speak-to-Chat pausa automaticamente a música quando você fala, e o modo ambiente permite ouvir sons importantes do ambiente quando necessário.', 199.99, 4, 23, '{"Drivers": "30mm tipo domo", "Resposta de Frequência": "4Hz - 40kHz", "Cancelamento de Ruído": "Dual Noise Sensor Technology", "Conectividade": "Bluetooth 5.2, LDAC, SBC, AAC", "Bateria": "30h com ANC, carregamento rápido", "Controles": "Touch panel, botões físicos", "Microfones": "8 microfones para ANC e chamadas", "Peso": "250g", "Dobráveis": "Sim, com estojo incluído", "Assistente de Voz": "Google Assistant, Alexa", "App": "Sony Headphones Connect", "Garantia": "1 ano"}', 1, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(4, 'Smart TV LG 55" 4K OLED C3', 'Smart TV OLED 55 polegadas com resolução 4K Ultra HD, tecnologia α9 Gen5 AI Processor.', 'A LG OLED C3 de 55 polegadas redefine a experiência de entretenimento doméstico com tecnologia OLED de última geração. Cada pixel se ilumina independentemente, criando pretos perfeitos e contraste infinito que torna cada cena cinematográfica incrivelmente realista.\n\nO processador α9 Gen5 AI utiliza inteligência artificial para otimizar automaticamente imagem e som, analisando o conteúdo em tempo real para oferecer a melhor experiência possível. Suporta todos os principais formatos HDR incluindo Dolby Vision IQ, HDR10 Pro e HLG, garantindo cores vibrantes e detalhes impressionantes.\n\nPara gamers, oferece recursos avançados como taxa de atualização de 120Hz, VRR (Variable Refresh Rate), ALLM (Auto Low Latency Mode) e suporte completo para HDMI 2.1, proporcionando jogabilidade fluida e responsiva em consoles de nova geração.\n\nO webOS 23 intuitivo oferece acesso fácil a todos os principais serviços de streaming, enquanto a compatibilidade com Alexa, Google Assistant e Apple HomeKit permite controle por voz e integração com ecossistemas de casa inteligente. O design ultrafino Gallery Design complementa qualquer decoração moderna.', 1899.99, 1, 12, '{"Tamanho": "55 polegadas", "Resolução": "4K Ultra HD (3840x2160)", "Tecnologia": "OLED evo", "Processador": "α9 Gen5 AI Processor 4K", "HDR": "Dolby Vision IQ, HDR10 Pro, HLG", "Taxa de Atualização": "120Hz", "Gaming": "VRR, ALLM, HDMI 2.1", "Sistema": "webOS 23", "Conectividade": "Wi-Fi 6, Bluetooth 5.0", "Portas": "4x HDMI 2.1, 3x USB, Ethernet", "Áudio": "Dolby Atmos, 40W", "Dimensões": "122.8 x 70.6 x 2.17 cm", "Peso": "18.7kg"}', 1, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(5, 'Mouse Gamer Logitech G Pro X Superlight', 'Mouse gamer profissional com sensor HERO 25K, até 25.600 DPI, switches mecânicos Lightspeed.', 'O Logitech G Pro X Superlight é o mouse definitivo para esports profissionais e gamers competitivos. Com peso de apenas 63 gramas, oferece movimentação ultrarrápida e precisa sem fadiga, mesmo durante sessões prolongadas de jogo.\n\nO sensor HERO 25K de próxima geração oferece precisão excepcional com DPI ajustável até 25.600, zero suavização, filtragem ou aceleração. A tecnologia de rastreamento sub-micrônico garante que cada movimento seja traduzido com perfeição absoluta no jogo.\n\nOs switches mecânicos LIGHTSPEED oferecem resposta instantânea com durabilidade testada para mais de 70 milhões de cliques. O design ambidestro permite uso confortável tanto para destros quanto canhotos, com botões laterais removíveis para personalização total.\n\nA conectividade sem fio LIGHTSPEED oferece latência de 1ms, mais rápida que muitos mouses com fio, enquanto a bateria dura até 70 horas de uso contínuo. Compatível com o sistema PowerPlay para carregamento sem fio contínuo durante o uso.', 89.99, 3, 35, '{"Sensor": "HERO 25K", "DPI": "100 - 25.600 (ajustável)", "Switches": "Mecânicos LIGHTSPEED", "Peso": "63g", "Conectividade": "LIGHTSPEED wireless 2.4GHz", "Bateria": "70 horas de uso", "Compatibilidade": "PowerPlay wireless charging", "Design": "Ambidestro", "Botões": "5 programáveis", "Polling Rate": "1000Hz (1ms)", "Dimensões": "125 x 63.5 x 40mm", "Garantia": "2 anos"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(6, 'Teclado Mecânico Corsair K95 RGB Platinum XT', 'Teclado mecânico gamer premium com switches Cherry MX Speed, iluminação RGB por tecla.', 'O Corsair K95 RGB Platinum XT é o teclado mecânico definitivo para gamers e entusiastas que exigem o máximo em performance e personalização. Construído com estrutura de alumínio aeronáutico escovado, oferece durabilidade excepcional e design premium.\n\nOs switches Cherry MX Speed Silver proporcionam ativação ultrarrápida com apenas 1.2mm de curso, ideais para jogos competitivos onde cada milissegundo conta. A tecnologia anti-ghosting com rollover de 100% garante que cada tecla pressionada seja registrada, mesmo durante combos complexos.\n\nA iluminação RGB por tecla com 16.7 milhões de cores permite personalização total através do software iCUE, com efeitos dinâmicos e sincronização com outros periféricos Corsair. As 6 teclas macro dedicadas podem ser programadas para executar comandos complexos com um único toque.\n\nO apoio para punho destacável em couro genuíno garante conforto durante longas sessões, enquanto os controles de mídia dedicados oferecem acesso rápido a volume e reprodução. A roda de volume de alumínio texturizada permite ajustes precisos sem tirar as mãos do jogo.', 299.99, 3, 18, '{"Switches": "Cherry MX Speed Silver", "Layout": "ABNT2 Português", "Iluminação": "RGB por tecla (16.7M cores)", "Anti-Ghosting": "100% com rollover completo", "Teclas Macro": "6 dedicadas programáveis", "Estrutura": "Alumínio aeronáutico escovado", "Apoio": "Couro genuíno destacável", "Controles": "Mídia dedicados + roda volume", "Software": "Corsair iCUE", "Conectividade": "USB 3.0 com passthrough", "Dimensões": "465 x 171 x 39mm", "Peso": "1.37kg"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(7, 'Monitor Gamer ASUS ROG Swift PG279QM 27"', 'Monitor gamer 27 polegadas, 240Hz, 1ms, G-Sync, resolução QHD 2560x1440.', 'O ASUS ROG Swift PG279QM estabelece novos padrões para monitores gaming com sua combinação única de alta resolução QHD e taxa de atualização extrema de 240Hz. Ideal para gamers competitivos que não querem comprometer qualidade visual por performance.\n\nO painel Fast IPS oferece cores vibrantes com 100% sRGB e tempos de resposta de apenas 1ms GTG, eliminando ghosting e motion blur mesmo nos jogos mais rápidos. A tecnologia NVIDIA G-SYNC garante sincronização perfeita entre GPU e monitor, eliminando tearing e stuttering.\n\nO design ROG distintivo inclui iluminação RGB Aura Sync personalizável e base ergonômica com ajustes completos de altura, inclinação, rotação e pivot. A conectividade abrangente inclui DisplayPort 1.4, HDMI 2.0 e hub USB 3.0 integrado.\n\nRecursos exclusivos como ASUS GamePlus oferecem crosshairs personalizáveis, timer e contador FPS, enquanto o GameVisual otimiza configurações de cor para diferentes tipos de jogos. O modo ELMB (Extreme Low Motion Blur) pode ser usado simultaneamente com G-SYNC para máxima clareza de movimento.', 1299.99, 3, 9, '{"Tamanho": "27 polegadas", "Resolução": "QHD 2560x1440", "Painel": "Fast IPS", "Taxa de Atualização": "240Hz", "Tempo de Resposta": "1ms GTG", "Tecnologia": "NVIDIA G-SYNC", "Cobertura de Cor": "100% sRGB", "Conectividade": "DisplayPort 1.4, HDMI 2.0", "USB Hub": "3.0 com 2 portas", "Ajustes": "Altura, inclinação, rotação, pivot", "Iluminação": "RGB Aura Sync", "Dimensões": "614 x 560 x 210mm"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(8, 'Webcam Logitech C920s HD Pro', 'Webcam Full HD 1080p com microfone estéreo, ideal para videoconferências e streaming.', 'A Logitech C920s HD Pro é a webcam mais popular do mundo para videoconferências profissionais e streaming. Com qualidade de vídeo Full HD 1080p a 30fps, garante imagem nítida e profissional em todas as suas chamadas e transmissões.\n\nA correção automática de luz HD ajusta automaticamente a exposição para condições de iluminação variáveis, enquanto o foco automático mantém a imagem sempre nítida. Os microfones estéreo com redução de ruído capturam áudio claro de até 1 metro de distância.\n\nO design compacto e versátil permite montagem em monitores, laptops ou tripés, com clipe universal que se adapta a telas de diferentes espessuras. A tampa de privacidade deslizante oferece segurança quando a webcam não está em uso.\n\nCompatível com todas as principais plataformas de videoconferência incluindo Zoom, Microsoft Teams, Skype e Google Meet. O software Logitech Capture permite personalização avançada com filtros, transições e gravação em múltiplas fontes.', 159.99, 2, 27, '{"Resolução": "Full HD 1080p (1920x1080)", "Taxa de Quadros": "30fps", "Campo de Visão": "78° diagonal", "Foco": "Automático", "Microfones": "Estéreo com redução de ruído", "Conectividade": "USB-A 3.0", "Compatibilidade": "Windows, macOS, Chrome OS", "Montagem": "Clipe universal + rosca tripé", "Privacidade": "Tampa deslizante", "Software": "Logitech Capture incluído", "Dimensões": "94 x 71 x 43.3mm", "Peso": "162g"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(9, 'SSD Kingston NV2 1TB NVMe PCIe 4.0', 'SSD NVMe PCIe 4.0 de 1TB, velocidades de leitura até 3.500 MB/s, ideal para upgrades.', 'O Kingston NV2 oferece performance PCIe 4.0 de próxima geração em um formato M.2 2280 compacto, ideal para upgrades de desktops e notebooks. Com velocidades de leitura sequencial de até 3.500 MB/s, transforma completamente a experiência de uso do computador.\n\nA tecnologia 3D NAND garante confiabilidade e durabilidade superiores, enquanto o controlador otimizado oferece eficiência energética para maior duração da bateria em laptops. O design sem DRAM mantém custos baixos sem comprometer significativamente a performance.\n\nPerfeito para acelerar inicialização do sistema, carregamento de aplicações e transferência de arquivos grandes. A capacidade de 1TB oferece espaço abundante para sistema operacional, programas e arquivos pessoais, eliminando a necessidade de gerenciamento constante de espaço.\n\nA instalação é simples e direta, compatível com a maioria dos sistemas modernos que suportam PCIe 4.0, mantendo retrocompatibilidade com PCIe 3.0. Inclui software de clonagem Kingston para migração fácil do sistema existente.', 249.99, 2, 42, '{"Capacidade": "1TB (1000GB)", "Interface": "PCIe 4.0 NVMe", "Formato": "M.2 2280", "Velocidade Leitura": "Até 3.500 MB/s", "Velocidade Escrita": "Até 2.100 MB/s", "Tecnologia": "3D NAND", "Controlador": "Phison E21T", "DRAM": "Sem cache DRAM", "TBW": "320 TBW", "MTBF": "2.000.000 horas", "Garantia": "3 anos", "Consumo": "2.1W ativo, 0.003W idle"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(10, 'Caixa de Som JBL Charge 5 Bluetooth', 'Caixa de som Bluetooth portátil, à prova d\'água IP67, 20 horas de bateria.', 'A JBL Charge 5 combina som poderoso com portabilidade extrema, oferecendo qualidade de áudio JBL Pro Sound em um design robusto e resistente. Com drivers otimizados e radiadores passivos, entrega graves profundos e médios/agudos cristalinos.\n\nA certificação IP67 garante proteção total contra água e poeira, permitindo uso em praias, piscinas e aventuras ao ar livre sem preocupações. A bateria de longa duração oferece até 20 horas de reprodução contínua, além de funcionar como power bank para carregar outros dispositivos.\n\nA conectividade Bluetooth 5.1 garante conexão estável até 10 metros de distância, enquanto a função PartyBoost permite conectar múltiplas caixas JBL compatíveis para criar um sistema de som ainda mais potente.\n\nO design cilíndrico com tecido durável e detalhes em borracha oferece pegada segura e estética moderna. Os controles intuitivos permitem ajustar volume, pular faixas e atender chamadas diretamente na caixa, com microfone integrado para chamadas viva-voz cristalinas.', 399.99, 4, 14, '{"Potência": "30W RMS", "Drivers": "Racetrack + radiadores passivos", "Conectividade": "Bluetooth 5.1", "Alcance": "10 metros", "Bateria": "20 horas de reprodução", "Power Bank": "Carrega dispositivos via USB", "Resistência": "IP67 (água e poeira)", "PartyBoost": "Conecta múltiplas caixas JBL", "Microfone": "Integrado para chamadas", "Dimensões": "220 x 96 x 93mm", "Peso": "960g", "Cores": "Múltiplas opções disponíveis"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 21:32:04', NULL, NULL),
	(11, 'Tablet Apple iPad Air 5ª Geração 64GB Wi-Fi', 'iPad Air com chip M1, tela Liquid Retina 10.9", 64GB, Wi-Fi, compatível com Apple Pencil.', 'O iPad Air de 5ª geração revoluciona a categoria de tablets com o poderoso chip M1, o mesmo processador dos MacBooks, oferecendo performance desktop em um formato ultrafino e leve. A tela Liquid Retina de 10.9 polegadas com tecnologia True Tone se adapta automaticamente à iluminação ambiente.\n\nO chip M1 com CPU de 8 núcleos e GPU de 8 núcleos oferece performance até 60% mais rápida que a geração anterior, permitindo edição profissional de vídeo 4K, jogos avançados e multitasking fluido com múltiplos aplicativos.\n\nCompatível com Apple Pencil de 2ª geração para desenho, anotações e criação artística com precisão de nível profissional. O Magic Keyboard transforma o iPad em um laptop completo para produtividade máxima.\n\nA câmera frontal Ultra Wide de 12MP com Center Stage mantém você sempre enquadrado durante videochamadas, enquanto a câmera traseira de 12MP captura fotos e vídeos impressionantes. Touch ID integrado ao botão superior oferece desbloqueio seguro e autenticação para compras.', 1899.99, 1, 7, '{"Processador": "Apple M1 (8 núcleos CPU + 8 núcleos GPU)", "Tela": "10.9\\" Liquid Retina (2360x1640)", "Armazenamento": "64GB", "Conectividade": "Wi-Fi 6", "Câmera Traseira": "12MP Wide", "Câmera Frontal": "12MP Ultra Wide com Center Stage", "Autenticação": "Touch ID", "Compatibilidade": "Apple Pencil 2ª gen, Magic Keyboard", "Bateria": "Até 10 horas de uso", "Sistema": "iPadOS 15", "Dimensões": "247.6 x 178.5 x 6.1mm", "Peso": "461g"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(12, 'Carregador Wireless Anker PowerWave 15W', 'Carregador sem fio 15W, compatível com iPhone e Android, design elegante e compacto.', 'O Anker PowerWave 15W oferece carregamento sem fio rápido e eficiente para todos os dispositivos compatíveis com Qi. Com potência máxima de 15W, carrega smartphones Android rapidamente, enquanto oferece 7.5W otimizados para iPhones.\n\nO design elegante e minimalista complementa qualquer ambiente, seja escritório, quarto ou sala de estar. A base antiderrapante mantém o carregador estável, enquanto a superfície texturizada evita que o dispositivo escorregue durante o carregamento.\n\nA tecnologia MultiProtect integrada oferece proteção contra sobretensão, sobrecorrente e superaquecimento, garantindo carregamento seguro para você e seu dispositivo. LEDs discretos indicam o status do carregamento sem incomodar durante a noite.\n\nCompatível com capas de até 5mm de espessura, elimina a necessidade de remover a proteção do telefone. Inclui cabo USB-C de 1.2m e adaptador de parede, oferecendo tudo necessário para começar a usar imediatamente.', 79.99, 6, 31, '{"Potência": "15W máxima (Android), 7.5W (iPhone)", "Padrão": "Qi wireless charging", "Compatibilidade": "iPhone 8+, Samsung Galaxy, outros Qi", "Proteção": "MultiProtect (sobretensão, corrente, temperatura)", "Indicadores": "LED de status", "Capas": "Compatível até 5mm", "Incluído": "Cabo USB-C 1.2m + adaptador", "Dimensões": "100 x 100 x 11mm", "Peso": "135g", "Certificações": "FCC, CE, RoHS", "Garantia": "18 meses", "Cor": "Preto"}', 0, 1, '2025-08-13 14:41:41', '2025-08-13 14:41:41', NULL, NULL),
	(13, 'Xbox 360', 'Video Game novo com desbloqueio.', 'O Xbox 360 fez parte da sétima geração de consoles, competindo diretamente contra o Playstation 3 e o Nintendo Wii. Ele trouxe muitas inovações em relação ao seu antecessor (o Xbox). Entre elas, temos o controle muito mais ergonômico e confortável, além de possuir gatilhos bastante ágeis e próprios para jogos de tiro em primeira pessoa. Este videogame utiliza DVD como sua mídia principal, mas era possível adquirir outros jogos na loja virtual. Entre os principais títulos do Xbox 360, temos a saga Halo, Gears of War e Alan Wake. Além disso, quando a Microsoft adquiriu a companhia de games Rare, vários outros nomes de peso se tornaram exclusivos, como Banjo Kazooie. Por fim, este console usava serviço de assinatura para permitir jogar online com seus amigos, sendo possível estabelecer chamadas e salas de conversa.', 799.99, 3, 0, '[]', 1, 1, '2025-08-13 17:33:55', '2025-08-13 22:11:46', NULL, NULL),
	(14, 'Xbox 360', 'Video Game novo com desbloqueio.', 'O Xbox 360 fez parte da sétima geração de consoles, competindo diretamente contra o Playstation 3 e o Nintendo Wii. Ele trouxe muitas inovações em relação ao seu antecessor (o Xbox). Entre elas, temos o controle muito mais ergonômico e confortável, além de possuir gatilhos bastante ágeis e próprios para jogos de tiro em primeira pessoa. Este videogame utiliza DVD como sua mídia principal, mas era possível adquirir outros jogos na loja virtual. Entre os principais títulos do Xbox 360, temos a saga Halo, Gears of War e Alan Wake. Além disso, quando a Microsoft adquiriu a companhia de games Rare, vários outros nomes de peso se tornaram exclusivos, como Banjo Kazooie. Por fim, este console usava serviço de assinatura para permitir jogar online com seus amigos, sendo possível estabelecer chamadas e salas de conversa.', 799.99, 3, 10, '{"Memoria Ram":"4GB","Armazenameto":"250GB","Tipo de Desbloqueio":"Rgh"}', 1, 1, '2025-08-13 17:35:05', '2025-08-13 17:35:05', NULL, NULL);

-- Copiando estrutura para tabela ecommerce_db.usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `tipo_usuario` enum('cliente','admin') DEFAULT 'cliente',
  `ativo` tinyint(1) DEFAULT 1,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuarios_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela ecommerce_db.usuarios: ~2 rows (aproximadamente)
REPLACE INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `cidade`, `cep`, `tipo_usuario`, `ativo`, `data_registro`, `ultimo_login`, `data_atualizacao`) VALUES
	(1, 'Administrador', 'admin@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, 'admin', 1, '2025-08-13 14:41:41', NULL, '2025-08-13 14:41:41'),
	(2, 'Usuário Teste', 'user@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, 'cliente', 1, '2025-08-13 14:41:41', NULL, '2025-08-13 14:41:41');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
