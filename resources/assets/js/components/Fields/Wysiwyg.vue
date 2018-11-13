<template>
    <div class="form-group">
        <label for="">{{ field.label }}
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>
        <div v-if="field.config.is_translatable">

            <input type="hidden"
                   :name="item.name"
                   v-model="item.value"
                   v-for="(item, index) in field.translations"
                   :key="item.name"
                   v-show="language === item.iso_code"
            >
            <VueEditor v-model="item.value"
                       v-for="(item, index) in field.translations"
                       :key="item.name + '-editor'"
                       v-show="language === item.iso_code"
            ></VueEditor>
        </div>
        <div v-else>
            <input type="hidden"
                   :name="field.name"
                   v-model="field.value"
            >
            <VueEditor v-model="field.value"
            ></VueEditor>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item']
    }
</script>
