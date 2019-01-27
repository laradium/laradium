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
            serverBus.$on('formatted', (data) => {
                this.items = this.flatToTree(data);
            });
        },

        methods: {
            flatToTree(array, parent, tree) {
                tree = typeof tree !== 'undefined' ? tree : [];
                parent = typeof parent !== 'undefined' ? parent : {id: '#'};

                let children = _.filter(array, (child) => {
                    return child.parent == parent.id;
                });

                if (!_.isEmpty(children)) {
                    if (parent.id == '#') {
                        tree = children;
                    } else {
                        parent['children'] = children
                    }
                    _.each(children, (child) => {
                        this.flatToTree(array, child)
                    });
                }

                return tree;
            }
        },
    }
</script>
