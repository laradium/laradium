<template>
    <div class="row m-b-15">
        <div class="col-md-12">
            <label>{{ field.label }}</label>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <gmap-autocomplete
                        class="form-control"
                        :value="field.value"
                        @place_changed="setPlace">
                </gmap-autocomplete>

                <input type="hidden" :name="field.name" v-model="field.value">
            </div>

            <div v-if="zoom.enabled" class="form-group">
                <label>Zoom</label>
                <input type="number" :name="zoom.name"
                       class="form-control" v-model.number="zoom.value">
            </div>
        </div>
        <div class="col-md-6">
            <GmapMap style="width: 100%; height: 300px;" :zoom="zoom.value" :center="center"
                     @zoom_changed="setZoom">
                <GmapMarker
                        v-if="showMarker"
                        :position="{lat: center.lat, lng: center.lng}"
                ></GmapMarker>
            </GmapMap>
        </div>

        <input type="hidden" :name="field.lat.name" :value="field.lat.value">
        <input type="hidden" :name="field.lng.name" :value="field.lng.value">
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],

        data() {
            let showMarker = false;
            let center = {
                lat: this.field.lat.value ? parseFloat(this.field.lat.value) : 24,
                lng: this.field.lng.value ? parseFloat(this.field.lng.value) : 56
            };

            let zoom = this.field.zoom;
            zoom.value = zoom.enabled ? parseInt(this.field.zoom.value) : 5;

            if (this.field.lat.value && this.field.lng.value) {
                showMarker = true;
            }

            return {
                showMarker: showMarker,
                place: null,
                zoom: zoom,
                center: center
            };
        },

        methods: {
            setDescription(description) {
                this.description = description;
            },

            setPlace(place) {
                this.place = place;
                this.center.lat = this.place.geometry.location.lat();
                this.center.lng = this.place.geometry.location.lng();
                this.field.value = this.place.formatted_address;
                this.field.lat.value = this.place.geometry.location.lat();
                this.field.lng.value = this.place.geometry.location.lng();
                this.showMarker = true;
            },

            setZoom(zoom) {
                this.zoom.value = zoom;
            },
        }
    }
</script>
