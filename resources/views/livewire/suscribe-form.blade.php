<div class="container">
    <form wire:submit.prevent="suscribe" class="form-horizontal">

        @include('shared.input',['label'=>'Email address', 'name'=>'email'])

        <button class="btn btn-outline-primary btn-rounded" type="submit">
            Suscribe
        </button>
    </form>
</div>
