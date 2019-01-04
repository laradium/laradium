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
                arr: [
                    {'id':1 ,'parent' : 0},
                    {'id':2 ,'parent' : 1},
                    {'id':3 ,'parent' : 1},
                    {'id':4 ,'parent' : 2},
                    {'id':5 ,'parent' : 0},
                    {'id':6 ,'parent' : 0},
                    {'id':7 ,'parent' : 4}
                ]
            };
        },

        created() {
            let $vm = this;
            serverBus.$on('formatted', function (data) {
                $vm.items = $vm.flatToTree(data);
            });
        },

        methods: {
            flatToTree( array, parent, tree ){
                let $vm = this;

                tree = typeof tree !== 'undefined' ? tree : [];
                parent = typeof parent !== 'undefined' ? parent : { id: '#' };

                let children = _.filter( array, function(child){ return child.parent == parent.id; });

                if( !_.isEmpty( children )  ){
                    if( parent.id == '#' ){
                        tree = children;
                    }else{
                        parent['children'] = children
                    }
                    _.each( children, function( child ){ $vm.flatToTree( array, child ) } );
                }

                return tree;
            }
        },
    }
</script>
