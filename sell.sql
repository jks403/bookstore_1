create or replace function sell_books (p_isbn     varchar2,
                                      p_qty_sold integer)
                  return integer is
    too_small_qty_sold exception;
    too_large_qty_sold exception;

    curr_qty_on_hand    integer;
    curr_order_point    integer;
    curr_auto_order_qty integer;

    new_qty_on_hand    integer;
begin
    -- starting with a commit because this is the beginning
    --     of a single, atomic transaction (to be completely
    --     done, or completely NOT done via a rollback)

    commit;

    -- gather information about this title that may be handy
    --    for this transaction (and which will conveniently
    --     throw an exception if the given ISBN is not in the
    --     title table)

    select qty_on_hand, order_point, auto_order_qty
    into   curr_qty_on_hand, curr_order_point, curr_auto_order_qty
    from   title
    where  isbn = p_isbn;

    -- quantity sold must be positive

    if p_qty_sold <= 0 then
        raise too_small_qty_sold;
    end if;

    -- are we trying to sell more than we currently have?

    if p_qty_sold > curr_qty_on_hand then
        raise too_large_qty_sold;
    end if;

    -- if reach here, it is safe to reduce qty_on_hand by the number sold

    new_qty_on_hand := curr_qty_on_hand - p_qty_sold;

    update title
    set qty_on_hand = new_qty_on_hand
    where isbn = p_isbn;

    -- does a new order for this title need to be placed
    --    now? Only if the quantity is now below the order point,
    --                 AND it isn't already on-order,
    --                 AND there isn't already a pending order_needed;

    if ( (new_qty_on_hand <= curr_order_point)
         and (is_on_order(p_isbn) = false)
         and (pending_order_needed(p_isbn) = false) ) then

        insert_order_needed(p_isbn, curr_auto_order_qty);
    end if;

    -- if get here -- all went well with the sale transaction

    commit;
    return 0;

exception
    -- if p_isbn does not exist in the title table

    when no_data_found then
        rollback;
        return -1;

    -- if someone tries to order a non-positive quantity of a book

    when too_small_qty_sold then
        rollback;
        return -2;

    -- if someone tries to buy more than the quantity on hand

    when too_large_qty_sold then
        rollback;
        return -3;

    -- if any other exception is thrown

    when others then
        rollback;
        return -4;
end;
/
show errors
