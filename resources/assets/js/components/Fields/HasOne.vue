<template>
    <transition name="fade">
        <div class="border" style="padding: 20px; border-radius: 5px; margin: 5px;">
            <h4>
                <i class="fa fa-bars"></i> {{ input.label }}
                <div class="pull-right">
                    <button class="btn btn-success btn-sm" @click.prevent="toggle()">
                        <span v-if="input.show"><i class="fa fa-eye-slash"></i> Hide</span>
                        <span v-else><i class="fa fa-eye"></i> Show</span>
                    </button>
                </div>
            </h4>
            <div v-show="input.show">
				<div class="row">
                    <div v-for="input in input.fields"
                         :class="input.col ? 'col-' + input.col.type + '-' + input.col.size : 'col-md-12'">
						<component :is="input.type + '-field'"
								   :input="input"
								   :replacementIds="replacementIds"
								   :language="language">
						</component>
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
                replacementIds: {}
            };
        },
		
        mounted() {
            if(this.input.id) {
                let id = this.input.id;
                let fields = this.input.fields;
                for (let field in fields) {
                    let repAttr = fields[field].replacementAttributes;

                    for(let ids in repAttr) {
                        if(!this.replacementIds[repAttr[ids]]) {
                            this.replacementIds[repAttr[ids]] = Math.random().toString(36).substring(7);
                        }
                    }

                    if(fields[field].isTranslatable) {
                        for (let attribute in fields[field].translatedAttributes) {
                            for(id in this.replacementIds) {
                                fields[field].translatedAttributes[attribute].name = fields[field].translatedAttributes[attribute].name.replace(id, this.replacementIds[id]);
                            }
                        }
                    } else {
                        for(id in this.replacementIds) {
                            fields[field].name = fields[field].name.replace(id, this.replacementIds[id]);
                        }
                    }

                }
            }
        },
		
		methods: {
			toggle() {
				this.input.show = !this.input.show;
			}
		}
    }
</script>
