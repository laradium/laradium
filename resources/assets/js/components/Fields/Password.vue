<template>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label :for="field.name">{{ field.label }}</label>

                <input type="password" :name="field.name" class="form-control" v-bind="attributes">
            </div>
        </div>

        <!--<div class="col-lg-6">-->
            <!--<div class="form-group">-->
                <!--<label :for="field.name + '_confirmation'">{{ field.label }} confirmation</label>-->

                <!--<input type="password" :name="field.name + '_confirmation'" class="form-control" v-bind="attributes">-->
            <!--</div>-->
        <!--</div>-->
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
