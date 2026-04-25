# Our Masterpieces Portfolio Section - Integration Guide

## Overview

The **Our Masterpieces** portfolio section is a premium, self-contained component designed for noorgee.pk/Web. It features GSAP-powered sliding-door animations, a responsive 3-column grid, bilingual support (English & Urdu), lazy-loaded media, and an interactive modal popup system.

---

## Features

✨ **GSAP Animations**
- Smooth sliding-door opening/closing effect
- Staggered grid item entrance animations
- Modal scale-in transitions
- Hover effects with smooth transforms

🎨 **Premium Design**
- Slate & Blue color scheme
- Responsive layout (mobile, tablet, desktop)
- Gradient overlays and depth effects
- Professional typography with Inter font

🌍 **Bilingual Support**
- Full English & Urdu support
- Proper typography: Inter (English) + Noto Nastaliq Urdu
- Easy language switching

📱 **Media Handling**
- Lazy-loaded images (.jpg, .png)
- HTML5 video support (.mp4)
- Optimized for fast loading

🎯 **Interactive Features**
- Category filtering (Web & Hosting, AI & Automation)
- Detailed project modal with tech stack badges
- Live preview links
- Smooth transitions and hover states

---

## Integration Methods

### Method 1: React Component (Recommended for Modern Sites)

**Best for:** React-based projects, Vite, Next.js

#### Installation

```bash
# Navigate to your project
cd your-project

# Install GSAP dependency
npm install gsap
# or
pnpm add gsap
```

#### Usage

1. **Copy the component files:**
   - `client/src/components/Masterpieces.tsx`
   - `client/src/components/Masterpieces.css`
   - `client/src/data/portfolioData.ts`

2. **Import and use in your page:**

```tsx
import Masterpieces from '@/components/Masterpieces';

export default function PortfolioPage() {
  return (
    <div>
      <h1>Our Work</h1>
      {/* English version */}
      <Masterpieces language="en" />
      
      {/* Or Urdu version */}
      <Masterpieces language="ur" />
    </div>
  );
}
```

3. **Customize portfolio data:**

Edit `client/src/data/portfolioData.ts` to add your projects:

```typescript
export const portfolioProjects: PortfolioProject[] = [
  {
    id: 'unique-id',
    titleEn: 'Project Title',
    titleUr: 'پروجیکٹ کا نام',
    categoryEn: 'Category',
    categoryUr: 'زمرہ',
    descriptionEn: 'Description...',
    descriptionUr: 'تفصیل...',
    goalsEn: 'Goals...',
    goalsUr: 'مقاصد...',
    techStack: ['React', 'Tailwind CSS'],
    mediaType: 'image', // or 'video'
    mediaUrl: 'https://...',
    liveLink: 'https://...',
    category: 'web', // or 'ai'
  },
  // Add more projects...
];
```

#### Styling

The component uses Tailwind CSS and custom CSS. Ensure your project has:

1. **Google Fonts in HTML head:**
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
```

2. **Tailwind CSS configured** (should be automatic in most React templates)

---

### Method 2: Standalone HTML/CSS/JS

**Best for:** Static sites, WordPress, traditional HTML projects

#### Usage

1. **Copy the standalone file:**
   - `STANDALONE_VERSION.html`

2. **Add to your page:**

```html
<!-- In your HTML file -->
<div id="portfolio-section"></div>

<!-- Load the standalone component -->
<script src="path/to/STANDALONE_VERSION.html"></script>
```

Or simply include the entire `STANDALONE_VERSION.html` file in your project and reference it as an iframe:

```html
<iframe src="path/to/STANDALONE_VERSION.html" style="width: 100%; border: none; height: 1200px;"></iframe>
```

#### Customization

Edit the `portfolioProjects` array in the script section:

```javascript
const portfolioProjects = [
  {
    id: 'web-1',
    titleEn: 'Your Project Title',
    categoryEn: 'Web & Hosting',
    descriptionEn: 'Your description...',
    goalsEn: 'Your goals...',
    techStack: ['Tech1', 'Tech2'],
    mediaType: 'image',
    mediaUrl: 'https://your-image-url.jpg',
    liveLink: 'https://your-live-link.com',
    category: 'web',
  },
  // Add more projects...
];
```

---

## Portfolio Data Structure

Each project requires the following properties:

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Unique identifier for the project |
| `titleEn` | string | Project title in English |
| `titleUr` | string | Project title in Urdu |
| `categoryEn` | string | Category in English |
| `categoryUr` | string | Category in Urdu |
| `descriptionEn` | string | Short description in English |
| `descriptionUr` | string | Short description in Urdu |
| `goalsEn` | string | Project goals in English |
| `goalsUr` | string | Project goals in Urdu |
| `techStack` | string[] | Array of technologies used |
| `mediaType` | 'image' \| 'video' | Type of media |
| `mediaUrl` | string | URL to image or video file |
| `liveLink` | string (optional) | URL to live project |
| `category` | 'web' \| 'ai' | Project category |

---

## Customization Guide

### Colors

Edit the CSS variables in `Masterpieces.css`:

```css
:root {
  --slate-50: #f8fafc;
  --slate-900: #0f172a;
  --blue-500: #3b82f6;
  --blue-600: #2563eb;
  /* ... adjust as needed ... */
}
```

### Animations

Modify GSAP animation parameters in `Masterpieces.tsx`:

```typescript
// Sliding door animation duration
gsap.to(doorLeftRef.current, {
  x: -50,
  opacity: 0,
  duration: 0.8, // Change this value
  ease: 'power2.inOut',
});
```

### Typography

Change font families in the component:

```tsx
style={{ fontFamily: language === 'ur' ? "'Noto Nastaliq Urdu'" : "'Inter'" }}
```

Or customize in CSS:

```css
.masterpieces-title {
  font-family: 'Your Font', sans-serif;
  font-size: 2.5rem;
  font-weight: 700;
}
```

### Responsive Breakpoints

Adjust grid columns in `Masterpieces.css`:

```css
@media (min-width: 768px) {
  .portfolio-grid {
    grid-template-columns: repeat(3, 1fr); /* Change 3 to desired columns */
    gap: 2.5rem;
  }
}
```

---

## Media Handling

### Image Optimization

1. **Use web-friendly formats:** `.jpg`, `.png`, `.webp`
2. **Recommended sizes:** 400x300px for grid items
3. **For best results:** Compress images before uploading

```bash
# Example using ImageMagick
convert input.jpg -resize 400x300 -quality 85 output.jpg
```

### Video Optimization

1. **Use `.mp4` format** for best browser compatibility
2. **Recommended bitrate:** 1-2 Mbps
3. **Recommended resolution:** 1280x720px or 1920x1080px

```bash
# Example using FFmpeg
ffmpeg -i input.mp4 -c:v libx264 -crf 23 -c:a aac -b:a 128k output.mp4
```

---

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## Performance Tips

1. **Lazy Loading:** Images use `loading="lazy"` attribute
2. **Optimize Media:** Compress images and videos before uploading
3. **CDN:** Host media files on a CDN for faster delivery
4. **Caching:** Enable browser caching for static assets

---

## Troubleshooting

### Component not rendering

**Problem:** Component appears blank or doesn't show.

**Solution:**
1. Ensure GSAP is installed: `npm install gsap`
2. Check that CSS file is imported
3. Verify portfolio data is not empty
4. Check browser console for errors

### Animations not working

**Problem:** Sliding doors or grid animations don't animate.

**Solution:**
1. Verify GSAP library is loaded
2. Check that refs are properly connected
3. Ensure CSS transitions aren't conflicting

### Modal not opening

**Problem:** Clicking portfolio items doesn't open modal.

**Solution:**
1. Check that modal backdrop element exists
2. Verify click handlers are attached
3. Check for JavaScript errors in console

### Bilingual text not displaying correctly

**Problem:** Urdu text appears incorrectly or overlaps.

**Solution:**
1. Ensure Noto Nastaliq Urdu font is loaded from Google Fonts
2. Check that `dir="rtl"` is set on Urdu text containers if needed
3. Verify font-family is correctly applied

---

## File Structure

```
noorgee-portfolio/
├── client/
│   └── src/
│       ├── components/
│       │   ├── Masterpieces.tsx      # Main component
│       │   └── Masterpieces.css      # Component styles
│       ├── data/
│       │   └── portfolioData.ts      # Portfolio data
│       └── pages/
│           ├── Home.tsx             # Home page
│           └── Demo.tsx             # Demo page with bilingual support
├── STANDALONE_VERSION.html          # Standalone HTML version
└── INTEGRATION_GUIDE.md             # This file
```

---

## Support & Customization

For additional customization or support:

1. **Review the component code** - Well-commented for easy understanding
2. **Check the demo page** - `Demo.tsx` shows bilingual usage
3. **Test in browser** - Use browser DevTools to inspect and debug
4. **Modify CSS** - All styling is in `Masterpieces.css` for easy tweaking

---

## License & Usage

This component is designed specifically for noorgee.pk and related projects. Feel free to customize and extend as needed.

---

## Version Info

- **GSAP Version:** 3.15.0
- **React Version:** 19.2.1
- **Tailwind CSS:** 4.1.14
- **Last Updated:** April 2026

---

**Happy building! 🚀**
