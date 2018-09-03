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
                        :name="item.name"
						:plugins="plugins"
						:toolbar1="toolbar1"
						:other="options"
						>
					</tinymce>
				</div>
			</div>
        </div>
        <div v-else>
            <tinymce :id="input.name" 
					:name="input.name" 
					v-model="input.value"
					:plugins="plugins"
					:toolbar1="toolbar1"
					:other="options"
					>{{ input.value }}</tinymce>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
        data: function () {
			return {
				plugins : [
					'advlist autolink lists link image charmap preview anchor textcolor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime media table contextmenu paste code directionality template colorpicker textpattern'
				],
				toolbar1: 'undo redo | bold italic strikethrough | forecolor backcolor | template link | bullist numlist | ltr rtl | removeformat',
				options: {
					height: 300
				}
			}
		}
    }
</script>
