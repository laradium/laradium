<template>
    <transition name="fade">
        <div>
            <h3 v-show="input.items.length">Widgets</h3>
            <draggable class="dragArea" :list="input.items" @update="onUpdate(input.items)" :options="draggable">
                <div class="col-md-12" v-for="(item, index) in input.items" :key="item.id">
                    <div class="panel">
                        <div class="panel-body border" style="padding: 20px; border-radius: 5px;">
                            <h4>
                                <i class="mdi mdi-arrow-all handle" v-if="item.isSortable"></i> {{ item.name }}
                                <div class="pull-right">
                                    <button class="btn btn-danger btn-sm" @click.prevent="remove(index, item.url)"><i
                                            class="fa fa-trash"></i></button>
                                </div>
                            </h4>
                            <div class="row">
                                <div v-for="input in item.fields"
                                     class="col-md-12">
                                    <component :is="input.type + '-field'"
                                               :input="input"
                                               :item="item"
                                               :replacementIds="replacementIds"
                                               :language="language">
                                    </component>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </draggable>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control" v-model="selectedWidget">
                            <option v-for="(item, index ) in input.template.widgets" :value="item" :selected="index == 0">{{ item }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary btn-sm" type="button" @click.prevent="addItem()">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        props: ['input', 'language'],
        data() {
            return {
                draggable: {
                    disabled: false,
                    handle: '.handle'
                },
                selectedWidget: '',
                replacementIds: {}
            };
        },
        mounted() {
            this.selectedWidget = this.input.template.widgets[0];
        },
        methods: {
            addItem() {
                let randId = Math.random().toString(36).substring(7);
                let template = JSON.parse(JSON.stringify(this.input.template.templates[this.selectedWidget]));
                template.id = randId;
                template.order = this.input.items.length + 1;

                for (let field in template.fields) {
                    console.log(this.replacementIds, 'asd')
                    let repAttr = template.fields[field].replacemenetAttributes;

                    let i = 1;
                    let idProp = '';
                    for(let ids in repAttr) {
                        if(!this.replacementIds[repAttr[ids]]) {
                            this.replacementIds[repAttr[ids]] = Math.random().toString(36).substring(7);
                        }

                        idProp = repAttr[ids];

                        i++;
                    }

                    if(idProp) {
                        this.replacementIds[idProp] = randId;
                    }

                    if(template.fields[field].type != 'morph-to') {
                        if (template.fields[field].isTranslatable) {
                            for (let attribute in template.fields[field].translatedAttributes) {
                                for(let id in this.replacementIds) {
                                    template.fields[field].translatedAttributes[attribute].name = template.fields[field].translatedAttributes[attribute].name.replace(id, this.replacementIds[id]);
                                }
                            }
                        } else {
                            for(let id in this.replacementIds) {
                                template.fields[field].name = template.fields[field].name.replace(id, this.replacementIds[id]);
                            }
                        }

                        template.fields[field].replacementIds = this.replacementIds;
                    } else {
                        template.fields[field].id = randId;
                    }
                }
                this.input.items.push(template);
            },
            onUpdate(items) {
                let i = 0;
                for (let i = 0; i < items.length; i++)
                    items[i].order = i;
                i++;
            },
            remove(item, url) {
                let id = item.id;

                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this item!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            if (url) {
                                axios({
                                    method: 'POST',
                                    url: url,
                                    data: {
                                        _method: 'delete',
                                        id: id
                                    }
                                });
                            }

                            this.input.items.splice(item, 1);

                            swal("Item has been deleted!", {
                                icon: "success",
                            });
                        }
                    });

            }
        }
    }
</script>
