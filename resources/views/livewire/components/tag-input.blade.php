<div class="tag-input-container" 
    x-data="{ 
        tags: @entangle('editingTags').defer,
        newTag: '',
        inputActive: false,
        addTag() {
            if (this.newTag.trim() !== '') {
                if (!this.tags.includes(this.newTag.trim())) {
                    this.tags.push(this.newTag.trim());
                }
                this.newTag = '';
            }
        },
        removeTag(tagToRemove) {
            this.tags = this.tags.filter(tag => tag !== tagToRemove);
        }
    }"
>
    <div class="tags-list">
        <template x-for="(tag, index) in tags" :key="index">
            <div class="tag">
                <span x-text="tag"></span>
                <button type="button" @click="removeTag(tag)" class="tag-remove">&times;</button>
            </div>
        </template>
    </div>
    
    <div class="tag-input-wrapper">
        <input 
            type="text"
            x-model="newTag"
            @keydown.enter.prevent="addTag()"
            @focus="inputActive = true"
            @blur="inputActive = false"
            placeholder="Add tag..."
            class="tag-input"
        >
        <button 
            type="button" 
            @click="addTag()" 
            class="tag-add-button"
        >
            Add
        </button>
    </div>
</div> 