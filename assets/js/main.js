/**
 * Master School ERP - Scripts Principais & Interatividade (ES6+)
 * Controla navegação animada, menu responsivo, modais e filtros de notícias
 */

document.addEventListener('DOMContentLoaded', () => {
  initNavbarScroll();
  initMobileMenu();
  initNewsFilters();
  initAnimateOnScroll();
});

/**
 * Efeito de Navbar condensada ao rolar a página
 */
function initNavbarScroll() {
  const navbar = document.querySelector('.navbar');
  if (!navbar) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
}

/**
 * Menu hambúrguer mobile animado
 */
function initMobileMenu() {
  const toggleBtn = document.querySelector('.mobile-toggle');
  const navMenu = document.querySelector('.nav-menu');

  if (!toggleBtn || !navMenu) return;

  toggleBtn.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    const isExpanded = navMenu.classList.contains('active');
    toggleBtn.innerHTML = isExpanded ? '✕' : '☰';
    toggleBtn.setAttribute('aria-expanded', isExpanded);
  });

  // Fechar ao clicar em um link
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      navMenu.classList.remove('active');
      toggleBtn.innerHTML = '☰';
    });
  });
}

/**
 * Filtro Interativo por Categorias no Mural de Notícias da Home
 */
function initNewsFilters() {
  const filterButtons = document.querySelectorAll('.news-filter-btn');
  const newsCards = document.querySelectorAll('.news-item');

  if (!filterButtons.length || !newsCards.length) return;

  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Atualiza botão ativo
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const filterValue = btn.getAttribute('data-filter');

      newsCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        if (filterValue === 'todos' || cardCategory === filterValue) {
          card.style.display = 'block';
          card.style.opacity = '1';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
}

/**
 * Animação suave na rolagem (Fade In em cards)
 */
function initAnimateOnScroll() {
  const cards = document.querySelectorAll('.glass-card, .news-card');
  if (!cards.length || !('IntersectionObserver' in window)) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  cards.forEach(card => {
    card.style.opacity = '0.9';
    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(card);
  });
}

/**
 * Exibe notificação visual Toast no topo da tela
 */
function showToast(message, type = 'info') {
  let toastContainer = document.getElementById('toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    toastContainer.style.cssText = `
      position: fixed;
      top: 90px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
    `;
    document.body.appendChild(toastContainer);
  }

  const colors = {
    info: '#3b82f6',
    success: '#10b981',
    warning: '#f59e0b',
    error: '#ef4444'
  };

  const toast = document.createElement('div');
  toast.style.cssText = `
    background: rgba(15, 23, 42, 0.95);
    border-left: 4px solid ${colors[type] || colors.info};
    color: #f8fafc;
    padding: 14px 20px;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideDown 0.3s ease;
  `;
  toast.innerHTML = `<span>${message}</span>`;
  toastContainer.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px)';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}
