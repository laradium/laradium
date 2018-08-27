<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <div v-if="input.isTranslatable">
            <input type="email"
                   v-model="item.value"
                   :key="index"
                   v-for="(item, index) in input.translatedAttributes"
                   v-show="language === item.iso_code"
                   class="form-control">
        </div>
        <div v-else>
            <input type="email" :value="input.value" :name="input.name" class="form-control">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item']
    }
</script>
