<crud-form url="{{ $form->getUrl() }}"
           method="PUT"
           form_data="{{ json_encode($form->data()) }}">
</crud-form>