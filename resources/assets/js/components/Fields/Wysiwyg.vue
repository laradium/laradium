<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <div v-if="input.isTranslatable">

            <input type="hidden"
                   :name="item.name"
                   v-model="item.value"
                   v-for="(item, index) in input.translatedAttributes"
                   :key="item.name"
                   v-show="language === item.iso_code"
            >
            <VueEditor v-model="item.value"
                       v-for="(item, index) in input.translatedAttributes"
                       :key="item.name"
                       v-show="language === item.iso_code"
            ></VueEditor>
        </div>
        <div v-else>
            <input type="hidden"
                   :name="input.name"
                   v-model="input.value"
            >
            <VueEditor v-model="input.value"
            ></VueEditor>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
        // data: function () {
		// 	// return {
		// 	// 	plugins : [
		// 	// 		'advlist autolink lists link image charmap preview anchor textcolor',
		// 	// 		'searchreplace visualblocks code fullscreen',
		// 	// 		'insertdatetime media table contextmenu paste code directionality template colorpicker textpattern'
		// 	// 	],
		// 	// 	toolbar1: 'undo redo | bold italic strikethrough | forecolor backcolor | template link | bullist numlist | ltr rtl | removeformat',
		// 	// 	options: {
		// 	// 		height: 300
		// 	// 	}
         //    return {};
		// 	}
		// }
        mounted() {

        }
    }
</script>
