<template>
    <fieldset class="relative w-full">
        <label class="border border-base-content/10 p-2 rounded flex flex-wrap gap-1 w-full">
            <span v-for="(tag, index) in tags" :key="index" class="badge badge-info badge-soft">
                {{ tag }}
                <div @click="removeTag(index)" class="ml-1 text-blue-500 hover:text-blue-700">
                    &times;
                </div>
            </span>
            <input ref="input-itself" type="text" @focus="isOpen = true" v-model="textInput" @keydown="onKeydown"
                @keydown.backspace="handleBackspace" class="grow p-1 text-sm outline-none"
                placeholder="Type and press Enter...">
        </label>

        <!-- Dropdown -->
        <ul v-if="isOpen && filteredOptions.length"
            class="absolute z-10 mt-1 w-full bg-base-300 rounded-md shadow-lg max-h-60 overflow-auto">
            <li v-for="(option, index) in filteredOptions" :key="option" @click="selectItem(option)" :class="[
                'cursor-pointer px-4 py-2 text-sm',
                index === selectedIndex ? 'bg-base-100' : 'hover:bg-base-100'
            ]">
                {{ option }}
            </li>
        </ul>
    </fieldset>
</template>


<script setup lang="ts">
import { computed, ref, useTemplateRef, watch } from 'vue';

const emit = defineEmits(['update:modelValue']);

const textInput = ref("");

// the input itself
const input = useTemplateRef<HTMLInputElement>("input-itself");


const isOpen = ref(false)
const selectedIndex = ref(-1)
const selectedItem = ref<string | null>(null)


const tags = ref<string[]>([]);
const model = defineModel({
    type: Array,
    default: () => []
});

const removeTag = (index: number) => {
    tags.value.splice(index, 1);
    emit("update:modelValue", tags.value);
    updateOptions();
}

const handleBackspace = () => {
    if (textInput.value === '' && tags.value.length > 0) {
        tags.value.pop();
        model.value = tags.value;
        
        emit("update:modelValue", tags.value);

        updateOptions();
    }
}

// Select item
function selectItem(item: string) {
    if (item && !tags.value.includes(item)) {
        tags.value.push(item)
        textInput.value = "";
    }

    model.value = tags.value;
    emit("update:modelValue", tags.value);

    selectedItem.value = item
    isOpen.value = false;
}

// Filtered options
const filteredOptions = computed(() =>
    options.value.filter(option =>
        option.toLowerCase().includes(textInput.value.toLowerCase())
    )
);

// Props or static options
const options = ref<string[]>([]);


const updateOptions = () =>
    options.value = props.items.filter(val => !tags.value.includes(val));

// Keyboard handling
function onKeydown(event: KeyboardEvent) {
    isOpen.value = true;

    if (!isOpen.value) return

    switch (event.key) {
        case 'ArrowDown':
            selectedIndex.value = (selectedIndex.value + 1) % filteredOptions.value.length;
            event.preventDefault();
            break;
        case 'ArrowUp':
            selectedIndex.value = (selectedIndex.value - 1 + filteredOptions.value.length) % filteredOptions.value.length;
            event.preventDefault();
            break;
        case 'Enter':
            const item = filteredOptions.value[selectedIndex.value];
            if (item) selectItem(item);
            event.preventDefault();
            break;
        default:
            break;
    }
}

const clearTags = () => tags.value = [];

const dropdownIsVisible = () => isOpen.value;

const hideDropdown = () => {
    isOpen.value = false;
    input.value?.blur();
}

watch(() => isOpen.value, (opened) => {
    if (opened) updateOptions();
});

const props = defineProps<{
    items: string[]
}>();

defineExpose({
    clearTags,
    dropdownIsVisible,
    hideDropdown
})

</script>
