<ul>
    @foreach($children as $child)
        <li>
            {{ $child->title }}
            @if(count($child->children))
                <ul>
                    @foreach($child->children as $grand_child)
                        <li>
                            {{ $grand_child->title }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
