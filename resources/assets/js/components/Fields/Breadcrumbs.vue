<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" aria-current="page" v-for="(breadcrumb, index) in field.breadcrumbs">
                <span v-if="index === count">{{ breadcrumb.name }}</span>
                <span v-else>
                <a :href="breadcrumb.url" v-html="breadcrumb.name"></a>
            </span>
            </li>
        </ol>
    </nav>
</template>

<script>
    export default {
        props: ['field', 'language'],
        data() {
            return {
                count: 0
            };
        },
        mounted() {
            this.count = this.field.breadcrumbs.length - 1;
        }
    }
</script>
