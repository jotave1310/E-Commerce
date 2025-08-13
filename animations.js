/**
 * Sistema de Animações Suaves - E-commerce Project
 * Versão 3.1.0 - Sem efeitos indesejados
 */

class EcommerceAnimations {
    constructor() {
        this.init();
    }

    init() {
        this.setupScrollAnimations();
        this.setupHoverEffects();
        this.setupFormAnimations();
        this.setupButtonEffects();
        this.setupChatbot();
        this.setupSmoothScrolling();
        this.setupLoadingAnimations();
    }

    // Animações de scroll suaves
    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    
                    // Adicionar classes de animação baseadas no elemento
                    if (element.classList.contains('product-card')) {
                        element.style.animation = 'fadeInUp 0.6s ease-out forwards';
                        element.style.animationDelay = `${Math.random() * 0.3}s`;
                    } else if (element.classList.contains('section-title')) {
                        element.style.animation = 'slideInLeft 0.8s ease-out forwards';
                    } else {
                        element.style.animation = 'fadeInUp 0.6s ease-out forwards';
                    }
                    
                    observer.unobserve(element);
                }
            });
        }, observerOptions);

        // Observar elementos para animação
        document.querySelectorAll('.product-card, .section-title, .hero h1, .hero p').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            observer.observe(el);
        });
    }

    // Efeitos de hover suaves
    setupHoverEffects() {
        // Efeito de hover nos cards de produto
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px) scale(1.02)';
                card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Efeito de hover nos botões
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'translateY(-2px)';
                btn.style.transition = 'all 0.2s ease-out';
            });

            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translateY(0)';
            });
        });

        // Efeito de hover no logo
        const logo = document.querySelector('.logo');
        if (logo) {
            logo.addEventListener('mouseenter', () => {
                logo.style.transform = 'scale(1.05)';
                logo.style.transition = 'transform 0.3s ease-out';
            });

            logo.addEventListener('mouseleave', () => {
                logo.style.transform = 'scale(1)';
            });
        }
    }

    // Animações de formulários
    setupFormAnimations() {
        // Efeito de foco nos inputs
        document.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('focus', () => {
                input.style.transform = 'translateY(-2px)';
                input.style.transition = 'all 0.3s ease-out';
                input.style.boxShadow = '0 4px 12px rgba(233, 69, 96, 0.2)';
            });

            input.addEventListener('blur', () => {
                input.style.transform = 'translateY(0)';
                input.style.boxShadow = 'none';
            });
        });

        // Validação visual em tempo real
        document.querySelectorAll('input[type="email"]').forEach(input => {
            input.addEventListener('input', () => {
                const isValid = input.value.includes('@') && input.value.length > 5;
                if (input.value.length > 0) {
                    input.style.borderColor = isValid ? '#28a745' : '#dc3545';
                    input.style.transition = 'border-color 0.3s ease-out';
                } else {
                    input.style.borderColor = '';
                }
            });
        });
    }

    // Efeitos de botões
    setupButtonEffects() {
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Efeito ripple
                const ripple = document.createElement('span');
                const rect = btn.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;
                
                btn.style.position = 'relative';
                btn.style.overflow = 'hidden';
                btn.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Adicionar keyframe para ripple
        if (!document.querySelector('#ripple-keyframes')) {
            const style = document.createElement('style');
            style.id = 'ripple-keyframes';
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Sistema de chatbot
    setupChatbot() {
        const chatbotToggle = document.querySelector('.chatbot-toggle');
        const chatbotWindow = document.querySelector('.chatbot-window');
        const chatbotClose = document.querySelector('.chatbot-close');
        const chatbotInput = document.querySelector('.chatbot-input');
        const chatbotSend = document.querySelector('.chatbot-send');
        const chatbotMessages = document.querySelector('.chatbot-messages');

        if (!chatbotToggle || !chatbotWindow) return;

        // Toggle chatbot
        chatbotToggle.addEventListener('click', () => {
            const isVisible = chatbotWindow.style.display === 'flex';
            chatbotWindow.style.display = isVisible ? 'none' : 'flex';
            
            if (!isVisible) {
                chatbotWindow.style.animation = 'fadeInUp 0.3s ease-out';
            }
        });

        // Fechar chatbot
        if (chatbotClose) {
            chatbotClose.addEventListener('click', () => {
                chatbotWindow.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    chatbotWindow.style.display = 'none';
                }, 300);
            });
        }

        // Enviar mensagem
        const sendMessage = () => {
            const message = chatbotInput?.value.trim();
            if (!message) return;

            // Adicionar mensagem do usuário
            this.addChatMessage(message, 'user');
            chatbotInput.value = '';

            // Simular resposta do bot
            setTimeout(() => {
                const response = this.getChatbotResponse(message);
                this.addChatMessage(response, 'bot');
            }, 1000);
        };

        if (chatbotSend) {
            chatbotSend.addEventListener('click', sendMessage);
        }

        if (chatbotInput) {
            chatbotInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }
    }

    // Adicionar mensagem ao chat
    addChatMessage(message, sender) {
        const chatbotMessages = document.querySelector('.chatbot-messages');
        if (!chatbotMessages) return;

        const messageDiv = document.createElement('div');
        messageDiv.style.cssText = `
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 12px;
            max-width: 80%;
            animation: fadeInUp 0.3s ease-out;
            ${sender === 'user' 
                ? 'background: linear-gradient(135deg, #e94560, #ff6b8a); color: white; margin-left: auto; text-align: right;'
                : 'background: rgba(255, 255, 255, 0.1); color: #ffffff; margin-right: auto;'
            }
        `;
        messageDiv.textContent = message;
        
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Respostas do chatbot
    getChatbotResponse(message) {
        const responses = {
            'ola': 'Olá! Como posso ajudá-lo hoje?',
            'produtos': 'Temos uma grande variedade de produtos eletrônicos, smartphones, notebooks e muito mais!',
            'preco': 'Nossos preços são muito competitivos! Você pode ver os valores na página de cada produto.',
            'entrega': 'Fazemos entregas para todo o Brasil! O prazo varia de 3 a 10 dias úteis.',
            'pagamento': 'Aceitamos cartão de crédito, débito, PIX e boleto bancário.',
            'ajuda': 'Estou aqui para ajudar! Você pode perguntar sobre produtos, preços, entrega ou qualquer dúvida.',
            'default': 'Obrigado pela sua mensagem! Nossa equipe está sempre pronta para ajudar. Você pode navegar pelos nossos produtos ou entrar em contato conosco.'
        };

        const lowerMessage = message.toLowerCase();
        for (const [key, response] of Object.entries(responses)) {
            if (lowerMessage.includes(key)) {
                return response;
            }
        }
        return responses.default;
    }

    // Scroll suave
    setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Animações de carregamento
    setupLoadingAnimations() {
        // Animação de entrada da página
        window.addEventListener('load', () => {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease-out';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Lazy loading para imagens
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.5s ease-out';
                    
                    setTimeout(() => {
                        img.style.opacity = '1';
                    }, 100);
                    
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Adicionar efeito LED nos produtos (conforme solicitado)
    addLEDEffect() {
        const style = document.createElement('style');
        style.textContent = `
            .product-card.led-effect {
                position: relative;
                overflow: hidden;
            }
            
            .product-card.led-effect::after {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, 
                    transparent, 
                    rgba(233, 69, 96, 0.3), 
                    rgba(255, 107, 138, 0.3), 
                    transparent
                );
                animation: ledSweep 3s ease-in-out infinite;
                pointer-events: none;
            }
            
            @keyframes ledSweep {
                0% { left: -100%; }
                50% { left: 100%; }
                100% { left: 100%; }
            }
        `;
        document.head.appendChild(style);

        // Aplicar efeito LED aos produtos em destaque
        document.querySelectorAll('.product-card').forEach((card, index) => {
            if (index % 2 === 0) { // Aplicar a produtos alternados
                card.classList.add('led-effect');
            }
        });
    }
}

// Inicializar animações quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    const animations = new EcommerceAnimations();
    
    // Adicionar efeito LED após 2 segundos
    setTimeout(() => {
        animations.addLEDEffect();
    }, 2000);
});

// Adicionar keyframes CSS para animações
const animationStyles = document.createElement('style');
animationStyles.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(20px);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .pulse {
        animation: pulse 2s ease-in-out infinite;
    }
`;
document.head.appendChild(animationStyles);

