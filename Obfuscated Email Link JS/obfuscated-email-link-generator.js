/*
$snippet_name = "obfuscated-email-link-generator";
$version = "<!#FV> 0.1.4 </#FV>";


 * Obfuscated Email Link Generator
 * Detects elements with email data attributes and creates functional mailto links
 */


class EmailLinkGenerator {
    constructor() {
        this.defaultTitles = {
            en: "Send email to",
            it: "Invia email a"
        };
        this.defaultCopyTitles = {
            en: "Copy e-mail",
            it: "Copia l'e-mail"
        };
        this.init();
    }

    /**
     * Get the document language from html lang attribute
     * @returns {string} Language code (defaults to 'en')
     */
    getDocumentLanguage() {
        const htmlLang = document.documentElement.lang || document.querySelector('html')?.getAttribute('lang');
        
        if (!htmlLang) {
            return 'en';
        }
        
        // Extract primary language code (e.g., 'en' from 'en-US')
        const primaryLang = htmlLang.toLowerCase().split('-')[0];
        
        // Return the language if we support it, otherwise default to English
        return this.defaultTitles[primaryLang] ? primaryLang : 'en';
    }

    /**
     * Generate default title text based on document language
     * @param {string} email - Full email address
     * @returns {string} Localized title text
     */
    generateDefaultTitle(email) {
        const lang = this.getDocumentLanguage();
        const defaultText = this.defaultTitles[lang];
        return `${defaultText} ${email}`;
    }

    /**
     * Generate default copy title text based on document language
     * @returns {string} Localized copy title text
     */
    generateDefaultCopyTitle() {
        const lang = this.getDocumentLanguage();
        return this.defaultCopyTitles[lang];
    }

    /**
     * Initialize the email link generator
     */
    init() {
        // Run on DOM content loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.processElements());
        } else {
            this.processElements();
        }
    }

    /**
     * Process all elements with email data attributes
     */
    processElements() {
        // Find all elements that have at least data-email and data-domain attributes
        const elements = document.querySelectorAll('[data-email][data-domain]');
        
        elements.forEach(element => {
            try {
                this.createMailtoLink(element);
            } catch (error) {
                console.warn('Error processing email element:', error, element);
            }
        });
    }

    /**
     * Create and attach mailto link to the specified target element
     * @param {HTMLElement} sourceElement - Element containing the data attributes
     */
    createMailtoLink(sourceElement) {
        const emailData = this.extractEmailData(sourceElement);
        
        if (!this.validateEmailData(emailData)) {
            console.warn('Invalid email data found:', emailData, sourceElement);
            return;
        }

        const mailtoUrl = this.buildMailtoUrl(emailData);
        const targetElement = this.findTargetElement(emailData.className, sourceElement);
        
        if (targetElement) {
            this.attachMailtoLink(targetElement, mailtoUrl, emailData);
        } else {
            console.warn(`Target element with class "${emailData.className}" not found`);
        }
    }

    /**
     * Extract email data from element attributes
     * @param {HTMLElement} element - Source element
     * @returns {Object} Email data object
     */
    extractEmailData(element) {
        return {
            email: element.dataset.email?.trim() || '',
            domain: element.dataset.domain?.trim() || '',
            subject: element.dataset.subject?.trim() || '',
            body: element.dataset.body?.trim() || '',
            className: element.dataset.class?.trim() || '',
            target: element.dataset.target?.trim() || '', // Added target attribute
            title: element.dataset.title?.trim() || '', // Added title attribute
            copyLink: element.dataset.copylink?.trim() || '', // Added copylink attribute
            copyLinkTitle: element.dataset.copylinkTitle?.trim() || '', // Added copylink-title attribute
            copyLinkIcon: element.dataset.copylinkIcon?.trim() || '' // Added copylink-icon attribute
        };
    }

    /**
     * Validate email data
     * @param {Object} emailData - Email data object
     * @returns {boolean} Validation result
     */
    validateEmailData(emailData) {
        // Check required fields
        if (!emailData.email || !emailData.domain) {
            return false;
        }

        // Basic email validation
        const emailRegex = /^[a-zA-Z0-9._-]+$/;
        const domainRegex = /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        return emailRegex.test(emailData.email) && domainRegex.test(emailData.domain);
    }

    /**
     * Build complete mailto URL
     * @param {Object} emailData - Email data object
     * @returns {string} Complete mailto URL
     */
    buildMailtoUrl(emailData) {
        const fullEmail = `${emailData.email}@${emailData.domain}`;
        let mailtoUrl = `mailto:${fullEmail}`;

        const params = [];
        
        if (emailData.subject) {
            params.push(`subject=${encodeURIComponent(emailData.subject)}`);
        }
        
        if (emailData.body) {
            params.push(`body=${encodeURIComponent(emailData.body)}`);
        }

        if (params.length > 0) {
            mailtoUrl += `?${params.join('&')}`;
        }

        return mailtoUrl;
    }

    /**
     * Find target element by class name
     * @param {string} className - Target class name
     * @param {HTMLElement} sourceElement - Source element for context
     * @returns {HTMLElement|null} Target element
     */
    findTargetElement(className, sourceElement) {
        if (!className) {
            // If no class specified, use the source element itself
            return sourceElement;
        }

        // First try to find within the same parent container
        const parent = sourceElement.closest('[data-email-container]') || sourceElement.parentElement || document;
        let targetElement = parent.querySelector(`.${className}`);

        // If not found in parent, search globally
        if (!targetElement) {
            targetElement = document.querySelector(`.${className}`);
        }

        return targetElement;
    }

    /**
     * Copy email to clipboard
     * @param {string} email - Email address to copy
     * @returns {Promise<boolean>} Success status
     */
    async copyToClipboard(email) {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                // Use modern clipboard API
                await navigator.clipboard.writeText(email);
                return true;
            } else {
                // Fallback for older browsers or non-HTTPS contexts
                const textArea = document.createElement('textarea');
                textArea.value = email;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                const result = document.execCommand('copy');
                document.body.removeChild(textArea);
                return result;
            }
        } catch (error) {
            console.warn('Failed to copy email to clipboard:', error);
            return false;
        }
    }

    /**
     * Create copy link button
     * @param {string} fullEmail - Complete email address
     * @param {Object} emailData - Email data object
     * @returns {HTMLElement} Copy link element
     */
    createCopyLinkButton(fullEmail, emailData) {
        const copyButton = document.createElement('a');
        copyButton.href = '#';
        copyButton.className = 'mk-copylink-btn';
        
        // Add icon if specified
        if (emailData.copyLinkIcon) {
            const iconElement = document.createElement('i');
            iconElement.className = `mk-copylink-icon ${emailData.copyLinkIcon}`;
            copyButton.appendChild(iconElement);
        }
        
        // Add text content
        if (emailData.copyLink) {
            const textNode = document.createTextNode(emailData.copyLink);
            copyButton.appendChild(textNode);
        }
        
        // Set title - use custom title or generate default
        const copyTitle = emailData.copyLinkTitle || this.generateDefaultCopyTitle();
        copyButton.title = copyTitle;
        
        // Add click event handler
        copyButton.addEventListener('click', async (e) => {
            e.preventDefault();
            const success = await this.copyToClipboard(fullEmail);
            
            if (success) {
                // Optional: Add visual feedback
                const originalTitle = copyButton.title;
                const lang = this.getDocumentLanguage();
                const successText = lang === 'it' ? 'E-mail copiata!' : 'Email copied!';
                copyButton.title = successText;
                
                // Reset title after 2 seconds
                setTimeout(() => {
                    copyButton.title = originalTitle;
                }, 2000);
            }
        });
        
        return copyButton;
    }

    /**
     * Attach mailto link to target element
     * @param {HTMLElement} targetElement - Element to receive the mailto link
     * @param {string} mailtoUrl - Complete mailto URL
     * @param {Object} emailData - Original email data for reference
     */
    attachMailtoLink(targetElement, mailtoUrl, emailData) {
        const fullEmail = `${emailData.email}@${emailData.domain}`;
        
        // If target is already a link, just update href
        if (targetElement.tagName.toLowerCase() === 'a') {
            targetElement.href = mailtoUrl;
            this.setLinkAttributes(targetElement, emailData, fullEmail);
            
            // Add copy link if specified
            if (emailData.copyLink !== '') {
                const copyButton = this.createCopyLinkButton(fullEmail, emailData);
                // Insert copy button after the mailto link
                targetElement.parentNode.insertBefore(copyButton, targetElement.nextSibling);
            }
        } else {
            // Create new link element
            const link = document.createElement('a');
            link.href = mailtoUrl;
            this.setLinkAttributes(link, emailData, fullEmail);

            // If target element has content, wrap it
            if (targetElement.innerHTML.trim()) {
                link.innerHTML = targetElement.innerHTML;
                targetElement.innerHTML = '';
                targetElement.appendChild(link);
            } else {
                // If target is empty, set default text
                link.textContent = fullEmail;
                targetElement.appendChild(link);
            }
            
            // Add copy link if specified
            if (emailData.copyLink !== '') {
                const copyButton = this.createCopyLinkButton(fullEmail, emailData);
                targetElement.appendChild(copyButton);
            }
        }

        // Add processed flag to avoid duplicate processing
        targetElement.dataset.emailProcessed = 'true';
    }

    /**
     * Set additional attributes for the mailto link
     * @param {HTMLElement} linkElement - Link element
     * @param {Object} emailData - Email data object
     * @param {string} fullEmail - Complete email address
     */
    setLinkAttributes(linkElement, emailData, fullEmail) {
        // Set title attribute - use custom title or generate default
        let titleText = emailData.title || this.generateDefaultTitle(fullEmail);
        
        // Add class for styling if not already present
        if (!linkElement.classList.contains('mailto-link')) {
            linkElement.classList.add('mailto-link');
        }

        // Store original email data as data attributes for reference
        linkElement.dataset.originalEmail = fullEmail;

        // Handle target attribute - only add target="_blank" if data-target is present
        if (emailData.target !== '') {
            linkElement.target = '_blank';
            // Add rel="noopener noreferrer" for security when opening in new tab
            linkElement.rel = 'noopener noreferrer';
            
            // Update title to indicate it opens in new tab
            const lang = this.getDocumentLanguage();
            const newTabText = lang === 'it' ? ' (Si apre in una nuova scheda)' : ' (Opens in new tab)';
            titleText += newTabText;
        }

        // Set the final title attribute
        linkElement.title = titleText;
    }

    /**
     * Manually process a specific element (useful for dynamic content)
     * @param {HTMLElement} element - Element to process
     */
    processElement(element) {
        if (element.dataset.email && element.dataset.domain && !element.dataset.emailProcessed) {
            this.createMailtoLink(element);
        }
    }

    /**
     * Refresh all email links (useful after DOM updates)
     */
    refresh() {
        // Remove processed flags and copy buttons
        document.querySelectorAll('[data-email-processed]').forEach(el => {
            delete el.dataset.emailProcessed;
        });
        
        // Remove existing copy buttons
        document.querySelectorAll('.mk-copylink-btn').forEach(btn => {
            btn.remove();
        });
        
        // Reprocess all elements
        this.processElements();
    }
}

// Auto-initialize when script loads
const emailLinkGenerator = new EmailLinkGenerator();

// Expose global methods for manual control
window.EmailLinkGenerator = {
    processElement: (element) => emailLinkGenerator.processElement(element),
    refresh: () => emailLinkGenerator.refresh(),
    instance: emailLinkGenerator
};
