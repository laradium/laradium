<template>
    <div>
        <div class="form-group" v-if="!input.isHidden">
            <h3>
                {{ input.label }}
            </h3>
            <div class="row">
                <div class="col-md-2" v-for="item in input.items">
                    <label :for="input.name + '_' + item.id">
                        <input type="checkbox" :checked="item.checked" :id="input.name + '_' + item.id" :name="input.name + '[]'" :value="item.id"> {{ item.name }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
    }
</script>
