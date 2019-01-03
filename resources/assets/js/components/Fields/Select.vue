<template>
    <div>
		<div class="form-group">
			<label for="">
				{{ field.label }}
				<span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
				<span class="badge badge-primary"
					  v-if="field.config.is_translatable">
					{{ language }}
				</span>
			</label>
			<div v-if="field.config.is_translatable">
				<select :name="item.name" 
						class="form-control"
						v-for="item in field.translations"
						v-show="language === item.iso_code"
						v-bind="attributes">
					<option
							:value="option.value"
							:selected="item.value === option.value"
							v-for="option in field.options">
						{{ option.text }}
					</option>
				</select>
			</div>
			<div v-else>
				<div v-if="!field.isHidden">
					<select :name="field.name" class="form-control" v-bind="attributes">
						<option
								:value="option.value"
								:selected="option.selected"
								v-for="option in field.options">
							{{ option.text }}
						</option>
					</select>
				</div>
				<div v-if="field.isHidden">
					<input type="hidden" :name="field.name" :value="field.default">
				</div>
			</div>
		</div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],

        computed: {
            attributes() {
                return this.field.attr;
            }
        }
    }
</script>
