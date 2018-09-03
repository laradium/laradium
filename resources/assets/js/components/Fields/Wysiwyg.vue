<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <div v-if="input.isTranslatable">
			<div v-for="(item, index) in input.translatedAttributes">
				<div v-show="language === item.iso_code">
                <tinymce
						:key=(index+1)
						:id="item.name"
                        v-model="item.value"
                        :name="item.name"></tinymce>
						</div>
			</div>
        </div>
        <div v-else>
            <tinymce :id="input.name" :name="input.name" v-model="input.value">{{ input.value }}</tinymce>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
        mounted() {

        }
    }
</script>
