<template>
    <div id="sidebar-menu">
        <ul>
            <li class="text-muted menu-title">Navigation</li>
        </ul>
        <menuitems :items="items" :active="active"></menuitems>
    </div>
</template>

<script>

    import {serverBus} from '../../laradium';

    export default {
        props: ['active'],

        data() {
            return {
                form_data: {},
                items: [],
            };
        },

        created() {
            let $vm = this;
            serverBus.$on('form_data', function (data) {
                $vm.items = data;
            });
        },
    }
</script>
