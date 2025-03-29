/**
 * Font Management Utility
 * This module handles font loading and configuration.
 * It allows for dynamic font changes throughout the application.
 */

// We don't need to import CSS here as the fonts will be imported in main.js
// Import our CSS variables for fonts

// Available font options - extend this object to add more fonts
const FONTS = {
  roboto: {
    name: 'Roboto',
    variable: 'var(--font-family)',
    weights: [300, 400, 500, 700],
    fallbacks: 'system-ui, -apple-system, "Segoe UI", sans-serif',
    description: 'Clean and modern sans-serif font'
  }
  // To add a new font:
  // 1. Install the font package: npm install @fontsource/[font-name]
  // 2. Add the font here with appropriate configuration
  // 3. Import the font files in the main.js
};

// Default font
const DEFAULT_FONT = 'roboto';

/**
 * Initialize fonts in the application
 */
export function initFonts() {
  // Check for saved font preference
  const savedFont = localStorage.getItem('font') || DEFAULT_FONT;
  setFont(savedFont);
}

/**
 * Set the application font
 * @param {string} fontKey - The key of the font to use (from FONTS object)
 * @returns {boolean} - Success status
 */
export function setFont(fontKey) {
  const font = FONTS[fontKey];
  
  if (!font) {
    console.error(`Font "${fontKey}" is not available.`);
    return false;
  }
  
  // Set CSS variable value
  document.documentElement.style.setProperty('--font-family', font.name + ', ' + font.fallbacks);
  
  // Save preference
  localStorage.setItem('font', fontKey);
  
  return true;
}

/**
 * Get the current font key
 * @returns {string} - Current font key
 */
export function getCurrentFont() {
  return localStorage.getItem('font') || DEFAULT_FONT;
}

/**
 * Get available fonts
 * @returns {Object} - Available fonts object
 */
export function getAvailableFonts() {
  return { ...FONTS };
} 