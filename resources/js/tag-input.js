/**
 * Tag input component for Alpine.js
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('tagInput', ({ name, selected = [], placeholder, existingTags = [] }) => ({
        name,
        selected,
        placeholder,
        existingTags,
        query: '',
        suggestions: [],
        isFocused: false,
        focusedIndex: 0,
        
        init() {
            // Convert the selected tags to the right format
            this.selected = this.formatSelectedTags();
        },
        
        formatSelectedTags() {
            return this.selected.map(tag => {
                // If it's already in the right format
                if (typeof tag === 'object' && tag.id && tag.name) {
                    return tag;
                }
                
                // If it's just an ID, try to find the tag in existingTags
                const id = typeof tag === 'object' ? tag.id : tag;
                const existingTag = this.existingTags.find(t => t.id == id);
                
                if (existingTag) {
                    return {
                        id: existingTag.id,
                        name: existingTag.name
                    };
                }
                
                // Fallback if we can't find the tag
                return {
                    id: id,
                    name: typeof tag === 'object' ? (tag.name || id) : id
                };
            });
        },
        
        search() {
            if (!this.query) {
                this.suggestions = [];
                return;
            }
            
            // Get suggestions from API
            fetch(`/api/v1/tags/suggest?query=${encodeURIComponent(this.query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.data) {
                        // Filter out already selected tags
                        this.suggestions = data.data.filter(
                            tag => !this.selected.some(s => s.id == tag.id)
                        );
                        this.focusedIndex = 0;
                    }
                })
                .catch(error => {
                    console.error('Error fetching tag suggestions:', error);
                });
        },
        
        selectTag(tag) {
            if (!tag) return;
            
            // Add tag to selected if not already selected
            if (!this.selected.some(t => t.id == tag.id)) {
                this.selected.push(tag);
            }
            
            // Reset query and suggestions
            this.query = '';
            this.suggestions = [];
            
            // Focus back on input
            this.$nextTick(() => {
                this.$refs.input.focus();
            });
        },
        
        removeTag(tag) {
            this.selected = this.selected.filter(t => t.id != tag.id);
            this.$refs.input.focus();
        },
        
        createTag() {
            if (!this.query.trim()) return;
            
            // Create a new tag with a temporary ID
            const newTag = {
                id: 'new_' + Date.now(),
                name: this.query.trim()
            };
            
            this.selectTag(newTag);
        },
        
        handleEnter() {
            if (this.suggestions.length > 0) {
                this.selectTag(this.suggestions[this.focusedIndex]);
            } else if (this.query.trim()) {
                this.createTag();
            }
        },
        
        handleArrowDown() {
            if (this.suggestions.length > 0) {
                this.focusedIndex = (this.focusedIndex + 1) % this.suggestions.length;
            }
        },
        
        handleArrowUp() {
            if (this.suggestions.length > 0) {
                this.focusedIndex = (this.focusedIndex - 1 + this.suggestions.length) % this.suggestions.length;
            }
        },
        
        handleBackspace() {
            if (!this.query && this.selected.length > 0) {
                this.selected.pop();
            }
        },
        
        handleDelete() {
            if (!this.query && this.selected.length > 0) {
                this.selected.pop();
            }
        },
        
        handleBlur() {
            // Delay hiding suggestions to allow for clicks
            setTimeout(() => {
                this.isFocused = false;
            }, 200);
        }
    }));
}); 