/**
 * Font management utility
 * This module handles font configuration and font switching
 */

// Font configuration with only Roboto for now, but structured for future extensibility
const fontConfig = {
  // Default font family
  defaultFont: 'Roboto',
  
  // Available font options (for future font switcher)
  availableFonts: {
    'Roboto': {
      package: '@fontsource/roboto',
      weights: [300, 400, 500, 700],
      styles: ['normal'],
    }
  }
};

/**
 * Initialize fonts by setting CSS variables
 */
export function initFonts() {
  // Load the user's preferred font from localStorage or use default
  const savedFont = localStorage.getItem('preferred-font');
  const fontToUse = savedFont && fontConfig.availableFonts[savedFont] 
    ? savedFont 
    : fontConfig.defaultFont;
  
  // Set CSS variables for the font
  document.documentElement.style.setProperty('--font-family', `'${fontToUse}', system-ui, -apple-system, sans-serif`);
}

/**
 * Change the current font
 * @param {string} fontName - Name of the font to switch to
 * @returns {boolean} Success status
 */
export function changeFont(fontName) {
  if (!fontConfig.availableFonts[fontName]) {
    console.error(`Font "${fontName}" is not available`);
    return false;
  }
  
  // Update CSS variables
  document.documentElement.style.setProperty('--font-family', `'${fontName}', system-ui, -apple-system, sans-serif`);
  
  // Save preference
  localStorage.setItem('preferred-font', fontName);
  return true;
}

/**
 * Get the current font configuration
 * @returns {Object} Current font configuration
 */
export function getFontConfig() {
  return { ...fontConfig };
}

export default {
  initFonts,
  changeFont,
  getFontConfig
}; 