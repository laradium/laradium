<template>
    <div class="modal fade" id="media-modal" tabindex="-1" role="dialog" aria-labelledby="mediaModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div hidden>
                        <input type="file" name="attachment-upload" multiple id="js-attachment-upload">
                    </div>

                    <button class="btn btn-success" @click.prevent="triggerFileUpload"
                            :disabled="is_upload_in_progress">

                        <span v-if="!is_upload_in_progress">
                            <i class="mdi mdi-cloud-upload"></i> Upload multiple
                        </span>
                        <span v-else>
                            <span class="btn-loader"></span>

                            Uploading...
                        </span>
                    </button>

                    <div class="col-md-4" style="position: absolute; right: 40px;">
                        <input @keyup="search" type="text" placeholder="Search..." class="form-control" autofocus>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="attachments.length">
                        <div class="col-md-3" v-for="attachment in attachments" :key="attachment.id">
                            <div class="card mb-3">
                                <div class="image-container text-center">
                                    <a :href="attachment.url" target="_blank">
                                        <span v-if="attachment.is_image">
                                            <img class="card-img-top"
                                                 :src="attachment.url"
                                                 :alt="attachment.name">
                                        </span>
                                        <span v-else>
                                            <h1 class="mt-5 text-secondary"
                                                v-text="attachment.extension"></h1>
                                        </span>
                                    </a>
                                </div>

                                <div class="btn-container mt-1">
                                    <button @click.prevent="remove(attachment)"
                                            class="btn btn-danger btn-sm float-right"
                                    >
                                        <i class="mdi mdi-delete"></i>
                                    </button>

                                    <button @click.prevent="copy(attachment)"
                                            class="btn btn-primary btn-sm btn-copy float-right mr-1"
                                    >
                                        <i class="mdi mdi-content-copy"></i>
                                    </button>

                                    <a :href="attachment.url" download
                                       class="btn btn-success btn-sm float-right mr-1"
                                    >
                                        <i class="mdi mdi-download"></i>
                                    </a>
                                </div>

                                <div class="card-body text-center">
                                    <a :href="attachment.url" target="_blank">
                                        <div v-text="attachment.name"></div>
                                    </a>
                                    <input type="hidden" :value="attachment.url" :id="'js-url-' + attachment.id">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div> Page {{pagination.current_page}} of {{pagination.last_page}}</div>
                    </div>
                    <div class="pagination text-center">
                        <button class="btn btn-primary mr-1" @click="getAttachments(pagination.prev_page_url)"
                                :disabled="!pagination.prev_page_url">
                            Previous
                        </button>
                        <button class="btn btn-primary" @click="getAttachments(pagination.next_page_url)"
                                :disabled="!pagination.next_page_url">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'MediaManager',
        data() {
            return {
                attachments: [],
                is_upload_in_progress: false,
                pagination: {},
                timer: null
            };
        },
        mounted() {
            $('#media-modal').on('shown.bs.modal', (e) => {
                $(e.target).find('[autofocus]').focus();
            });

            this.getAttachments();

            $('#js-attachment-upload').on('change', async (event) => {
                this.is_upload_in_progress = true;

                let input = event.target;
                let form_data = new FormData();
                let files = input.files;

                for (let index in files) {
                    form_data.append('files[]', files[index]);
                }

                try {
                    let request = await axios.post('/admin/attachments/upload', form_data);
                    let response = request.data;

                    this.attachments = response.data;

                    this.is_upload_in_progress = false;

                } catch (error) {
                    console.log(error);
                }
            });
        },
        created() {

        },
        methods: {
            search(e) {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }

                this.timer = setTimeout(() => {
                    this.getAttachments(null, e.target.value);
                }, 500);
            },
            /**
             *
             * @param url
             */
            async getAttachments(url = null, search = null) {
                try {
                    let fullUrl = url ? url : '/admin/attachments';
                    let request = await axios.get(fullUrl + (search ? '?search=' + search : ''));
                    let response = request.data;

                    this.attachments = response.data;
                    this.setPaginationLinks(response);
                } catch (error) {
                    console.log(error);

                    return [];
                }
            },
            /**
             *
             * @param data
             */
            setPaginationLinks(data) {
                this.pagination = {
                    current_page: data.meta.current_page,
                    last_page: data.meta.last_page,
                    next_page_url: data.links.next,
                    prev_page_url: data.links.prev
                };
            },
            triggerFileUpload() {
                $('#js-attachment-upload').trigger('click');
            },
            /**
             *
             * @param attachment
             */
            copy(attachment) {
                let selector = '#js-url-' + attachment.id;

                let testingCodeToCopy = document.querySelector(selector);
                testingCodeToCopy.setAttribute('type', 'text');
                testingCodeToCopy.select();

                try {
                    document.execCommand('copy');
                    toastr.success('Successfully copied to the clipboard');
                } catch (err) {
                    console.log('Oops, unable to copy');
                }

                /* unselect the range */
                testingCodeToCopy.setAttribute('type', 'hidden');
                window.getSelection().removeAllRanges();

            },
            /**
             *
             * @param attachment
             */
            async remove(attachment) {
                let result = await swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this after you press 'Save'!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                });

                if (result.value) {
                    try {
                        await axios.delete('/admin/attachments/' + attachment.id);

                        let attachmentIndex = this.attachments.indexOf(attachment);

                        if (attachmentIndex > -1) {
                            this.attachments.splice(attachmentIndex, 1);
                        }

                        toastr.success('Attachment successfully deleted!');

                    } catch (error) {
                        console.log(error);
                    }

                    return true;
                }
            }
        }
    }
</script>