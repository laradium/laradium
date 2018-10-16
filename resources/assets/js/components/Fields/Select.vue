<template>
    <div>
		<div class="form-group">
			<label for="">{{ input.label }}
				<span class="badge badge-primary"
					  v-if="input.isTranslatable">
					{{ language }}
				</span>
			</label>
			<div v-if="input.isTranslatable">
				<select :name="item.name" 
						class="form-control"
						v-for="item in input.translatedAttributes"
						v-show="language === item.iso_code"
						v-bind="attributes">
					<option
							:value="option.value"
							:selected="item.value === option.value"
							v-for="option in input.options">
						{{ option.text }}
					</option>
				</select>
			</div>
			<div v-else>
				<div v-if="!input.isHidden">
					<select :name="input.name" id="" class="form-control" v-bind="attributes">
						<option
								:value="option.value"
								:selected="option.selected"
								v-for="option in input.options">
							{{ option.text }}
						</option>
					</select>
				</div>
				<div v-if="input.isHidden">
					<input type="hidden" :name="input.name" :value="input.default">
				</div>
			</div>
		</div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],

        computed: {
            attributes() {
                return this.input.attr;
            }
        }
    }
</script>
