<template>
    <ul :class="{'list-unstyled': is_child}">
        <li v-for="item in items" v-if="item.formatted && item.formatted.has_permission" :class="{has_sub: item.children.length}">
            <a :href="item.children.length ? 'javascript:;': item.formatted.url"
               :class="{'active' : item.formatted.url.includes(active), 'waves-effect': item.children.length}">
                <i v-if="item.formatted.icon" :class="item.formatted.icon"></i>
                <i v-else class="mdi mdi-view-dashboard"></i>
                <span> {{ item.formatted.name }} </span>
                <div class="pull-right">
                    <span class="fa fa-caret-down" v-if="item.children.length"></span>
                </div>
            </a>
            <menuitems v-if="item.children.length" :items="item.children" :is_child="true" :active="active"></menuitems>
        </li>
    </ul>
</template>

<script>
    export default {
        props: ['items', 'is_child', 'active'],
    }
</script>
