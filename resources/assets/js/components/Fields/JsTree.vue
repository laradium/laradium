<template>
    <div class="form-group">
        <div id="js-tree"></div>
    </div>
</template>

<script>
    import {serverBus} from '../../laradium';

    export default {
        props: ['tree', 'field'],

        data () {
            return {
                has_new_items: false,
                new_item_count: 0
            };
        },

        created() {
            let $vm = this;

            serverBus.$on('added_tree_item', function (id) {
                $vm.new_item_count =  $vm.new_item_count + 1;

                $('#js-tree').jstree().create_node('#', {"id": id, "text": "NEW ITEM (" + $vm.new_item_count + ")"}, "last", function () {
                    $vm.has_new_items = true;
                });

                let entries = $vm.field.entries;
                for (let entry in entries) {
                    if (entries[entry].id == id) {
                        entries[entry].config.is_collapsed = false;
                    } else {
                        entries[entry].config.is_collapsed = true;
                    }
                }
            });
        },

        mounted: function () {
            let $vm = this;
            let tree = $('#js-tree');
            let field = this.field;

            tree.jstree({
                'core': {
                    'data': this.tree,
                    "check_callback": function (op, node, parent, position, more) {
                        if (op === "move_node" && more && more.core) {
                            if ($vm.has_new_items) {
                                toastr.warning('You need to save new data before sorting!');

                                return false;
                            }
                        }

                        return true;
                    }
                },
                'plugins': [
                    "dnd"
                ]
            }).bind("move_node.jstree", function () {
                let modified_tree = tree.jstree(true).get_json('#', {flat: true});
                serverBus.$emit('formatted', modified_tree);

                let i = 0;
                for (let item in modified_tree) {
                    let entries = field.entries;
                    let fields = [];
                    for (let entry in entries) {
                        if (entries[entry].id == modified_tree[item].id) {
                            fields = entries[entry].fields;
                            for (let field in fields) {
                                if (fields[field].label === 'Parent id') {
                                    fields[field].value = (modified_tree[item].parent !== '#' ? modified_tree[item].parent : null);
                                }

                                if (fields[field].label === 'Sequence no') {
                                    fields[field].value = i;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                    i++;
                }
            }).on('loaded.jstree', function () {
                tree.jstree('open_all');

                let modified_tree = tree.jstree(true).get_json('#', {flat: true});
                serverBus.$emit('formatted', modified_tree);
            }).on("select_node.jstree", function (e, data) {
                let entries = field.entries;
                for (let entry in entries) {
                    if (entries[entry].id == data.node.id) {
                        entries[entry].config.is_collapsed = !entries[entry].config.is_collapsed;
                    } else {
                        entries[entry].config.is_collapsed = true;
                    }
                }
            });


        },
    };
</script>
