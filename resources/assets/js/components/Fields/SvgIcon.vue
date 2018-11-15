<template>
    <div>
        <div class="form-group">
            <label for="">{{ field.label }}
                <span class="badge badge-primary"
                      v-if="field.config.is_translatable">
					{{ language }}
				</span>
            </label>

            <select :name="field.name" class="form-control" v-bind="attributes">
                <option
                        :value="file_name"
                        :selected="file_name === field.value"
                        v-for="(name, file_name) in field.options">
                    {{ name }}
                </option>
            </select>
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
