# 🎨 Design System e Estética Visual — Master School ERP

O **Design System** do **Master School ERP** foi criado a partir do zero em Vanilla CSS (`assets/css/design-system.css`), utilizando variáveis HSL dinâmicas e o efeito óptico **Glassmorphism** (*Frosted Glass*).

---

## 1. Paleta de Cores (HSL Tokens)

O uso de `HSL` (Matiz, Saturação e Luminosidade) permite ajustes fluidos de tom e opacidade entre superfícies claras e escuras:

```css
:root {
    /* Superfícies e Fundos */
    --bg-dark: hsl(222, 47%, 10%);       /* #0a1128 - Deep Navy */
    --bg-card: hsl(222, 40%, 15%);       /* Superfície do card escuro */
    
    /* Cores de Destaque */
    --primary: hsl(217, 91%, 60%);       /* #3b82f6 - Azul Real Vibrante */
    --primary-hover: hsl(217, 91%, 50%); /* Azul mais profundo para hover */
    --accent: hsl(43, 96%, 56%);         /* #fbbf24 - Ouro Dourado Imperial */
    --success: hsl(158, 64%, 52%);       /* #10b981 - Verde Esmeralda */
    --danger: hsl(0, 84%, 60%);          /* #ef4444 - Vermelho Carmim */
}
```

---

## 2. Efeito Vidro Fosco (Glassmorphism)

Todas as superfícies interativas, cards de métricas e formulários aplicam o efeito de vidro difuso com reflexo de luz e borda semi-translúcida:

```css
.glass-card {
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    transition: transform 0.3s ease, border-color 0.3s ease;
}

.glass-card:hover {
    border-color: rgba(59, 130, 246, 0.3);
    transform: translateY(-2px);
}
```

---

## 3. Tipografia (Outfit & Inter)

O projeto importa o acervo **Google Fonts** com pesos tipográficos precisos para diferentes escalas:
- **Outfit (600, 700, 800):** Reservada para cabeçalhos `<h1>` a `<h4>`, números estatísticos em cards do painel e mensagens de destaque.
- **Inter (400, 500, 600):** Utilizada em parágrafos de texto, tabelas financeiras, listas de alunos e formulários.

---

## 4. Navegação Responsiva (Mobile Drawer)

O menu institucional do portal web (`assets/js/main.js`) detecta viewports mobile (< 992px) e converte automaticamente a barra horizontal em uma gaveta lateral animada (*Off-canvas drawer*), garantindo acessibilidade em smartphones sem quebrar a consistência visual.
