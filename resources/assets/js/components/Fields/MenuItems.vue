<template>
    <ul :class="{'list-unstyled': is_child}">
        <li v-for="item in items"
            v-if="item.data && item.data.has_permission"
            :class="{has_sub: item.children && item.children.length, 'active' : item.data.url.includes(active) || (item.children && item.children.filter(i => i.data.url.includes(active)).length > 0) }">
            <a :href="item.children && item.children.length ? 'javascript:;': item.data.url"
               :class="{'active' : item.data.url.includes(active) || (item.children && item.children.filter(i => i.data.url.includes(active)).length > 0), 'waves-effect': item.children && item.children.length}">
                <i v-if="item.data.icon" :class="item.data.icon"></i>
                <i v-else class="mdi mdi-view-dashboard"></i>
                <span> {{ item.data.name }} </span>
                <div class="pull-right">
                    <span class="fa fa-caret-down" v-if="item.children && item.children.length"></span>
                </div>
            </a>
            <menuitems v-if="item.children && item.children.length" :items="item.children" :is_child="true"
                       :active="active"></menuitems>
        </li>
    </ul>
</template>

<script>
    export default {
        props: ['items', 'is_child', 'active'],
        created() {
            $(document).find('li.has_sub a.active').next('ul').slideDown();
        }
    }
</script>
