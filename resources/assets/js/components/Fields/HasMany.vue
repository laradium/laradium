<template>
    <transition name="fade">
        <div class="border"  style="padding: 20px; border-radius: 5px; margin: 5px;">
            <h4>
                <i class="fa fa-bars"></i> {{ input.label }}s
                <div class="pull-right">
                    <button class="btn btn-success btn-sm" @click.prevent="toggle()">
						<span v-if="input.show"><i class="fa fa-eye-slash"></i> Hide</span>
						<span v-else><i class="fa fa-eye"></i> Show</span>
					</button>
                </div>
            </h4>
            <div v-show="input.show">
                <draggable class="dragArea" :list="input.items" @update="onUpdate(input.items)" :options="draggable">
                    <div class="col-md-12" v-for="(item, index) in input.items" :key="item.id">
                        <div class="panel" style="padding: 5px;">
                            <div class="panel-title">
                                <h4>
                                    <i class="mdi mdi-arrow-all handle" v-if="input.is_sortable"></i>
                                    <div class="pull-right">
                                        <button class="btn btn-danger btn-sm" @click.prevent="remove(index, item.url, item.resource)"><i
                                                class="fa fa-trash"></i></button>
                                    </div>
                                </h4>
                            </div>
                            <div class="panel-body border" style="padding: 20px; border-radius: 2px;">
                                <div class="row">
                                    <div v-for="input in item.fields"
                                         :class="input.col ? 'col-' + input.col.type + '-' + input.col.size : 'col-md-12'">
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
                <button class="btn btn-primary btn-sm" type="button" @click.prevent="addItem()">
                    <i class="fa fa-plus"></i> Add {{ input.label }}
                </button>
                <br><br>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        props: ['input', 'language', 'replacementIds'],
        data() {
            return {
                draggable: {
                    disabled: true,
                    handle: '.handle'
                }
            };
        },
        mounted() {
            this.draggable.disabled = !this.input.is_sortable;
        },
        methods: {
            addItem() {
                let randId = Math.random().toString(36).substring(7);
                let template = JSON.parse(JSON.stringify(this.input.template));
                template.id = randId;
                template.order = this.input.items.length;
                // console.log(123, template.fields);
                for (let field in template.fields) {
                    // console.log(template.fields[field].replacemenetAttributes);
                    let repAttr = template.fields[field].replacemenetAttributes;
                    // console.log(123, repAttr);

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
            remove(item, url, resource) {
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
                                        id: id,
                                        resource: resource
                                    }
                                });
                            }

                            this.input.items.splice(item, 1);

                            swal("Item has been deleted!", {
                                icon: "success",
                            });
                        }
                    });

            },
            toggle() {
                this.input.show = !this.input.show;
            }
        }
    }
</script>
