<template>
    <section v-if="visibleImages.length >= 2" class="bg-[#F2ECE3] pt-20 pb-20 md:pt-28 md:pb-28">
        <div class="site-shell">
            <div class="card-frame bg-white">
                <h2 class="section-heading text-center">{{ content.heading || "Photo's of us across the years" }}</h2>
                <div class="mt-10 space-y-4 md:space-y-5">
                    <div
                        v-for="(row, rowIndex) in imageRows"
                        :key="`gallery-row-${rowIndex}`"
                        class="gallery-row grid gap-4 md:gap-5"
                        :style="{ '--gallery-columns': row.length }"
                    >
                        <figure
                            v-for="(item, itemIndex) in row"
                            :key="`${rowIndex}-${itemIndex}-${item.image}`"
                            class="aspect-square overflow-hidden border border-soft bg-[#F7F7F7] shadow-soft"
                        >
                            <img
                                :src="item.image"
                                alt="Couple memory"
                                loading="lazy"
                                class="h-full w-full object-cover"
                                :style="{ objectPosition: `${item.imageFocusX ?? 50}% ${item.imageFocusY ?? 50}%` }"
                            >
                        </figure>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({
            heading: "Photo's of us across the years",
            items: [],
        }),
    },
});

const visibleImages = computed(() =>
    (Array.isArray(props.content.items) ? props.content.items : [])
        .filter((item) => item?.image)
        .slice(0, 8)
);

const imageRows = computed(() => {
    const images = visibleImages.value;
    const count = images.length;

    if (count <= 4) {
        return [images];
    }

    if (count === 5) {
        return [images.slice(0, 3), images.slice(3)];
    }

    if (count === 6) {
        return [images.slice(0, 3), images.slice(3)];
    }

    if (count === 7) {
        return [images.slice(0, 4), images.slice(4)];
    }

    return [images.slice(0, 4), images.slice(4, 8)];
});
</script>

<style scoped>
.gallery-row {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 1024px) {
    .gallery-row {
        grid-template-columns: repeat(var(--gallery-columns), minmax(0, 1fr));
    }
}
</style>
