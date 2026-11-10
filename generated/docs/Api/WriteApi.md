# ShadowSoftware\DabDash\WriteApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**abandonedCartRecoveryManage()**](WriteApi.md#abandonedCartRecoveryManage) | **POST** /api/v1/tools/abandoned_cart_recovery_manage | Show or configure one-time abandoned-cart recovery emails for a tenant. Eligible carts are sent to an account or guest email captured during checkout; carts must be idle for the configured delay and not already recovered. Sending also requires complete tenant SMTP settings. This tool does not send immediately.  ACTIONS:   show (default): current enabled state and delay.   set: pass enabled and/or delay_hours. Omitted values are preserved.   dry_run defaults true; pass dry_run&#x3D;false to save. |
| [**blogImport()**](WriteApi.md#blogImport) | **POST** /api/v1/tools/blog_import | Create many blog posts (and the storefront&#39;s legal pages) in one call. tenant_blog_upsert can only edit an existing post; this creates new ones.  Pass posts with title and body (HTML), and optionally slug, excerpt, category, tags, meta_title, meta_description, published (default true), published_at (ISO date) and image_url (downloaded into the media library and used as the featured image). Posts are matched by slug within the store: existing ones are skipped unless update_existing&#x3D;true, in which case only the fields you pass are changed.  PRIVACY: a privacy-policy post remains at its own /blog/{slug} URL. When it is the store&#39;s policy, set that exact published URL with store_settings_manage privacy_url. Terms and refund remain available at their storefront policy URLs.  dry_run defaults true: the first call reports what would be created, updated or skipped. Pass dry_run&#x3D;false to write. Image failures never block a post. |
| [**bundleHealth()**](WriteApi.md#bundleHealth) | **POST** /api/v1/tools/bundle_health | Read-only. Tenant-wide bundle health audit — the MCP equivalent of the vendor-facing /admin/bundles/health page (GitHub #395). Surfaces price-ladder conflicts: a variation currently sitting in 2+ active &#x60;fixed_total&#x60; bundles at different price points, which is exactly the \&quot;14 variations stuck in two price rungs\&quot; leak the original issue was about. A synchronous guard already prevents most NEW conflicts at save time, but this catches drift from bulk imports, seeders, or any write that bypassed Eloquent.  Each conflict lists the variation and every bundle it&#39;s in with that bundle&#39;s shared member price (its \&quot;rung\&quot;), so you can tell which bundle is now stale for that variation and remove it (via bundle_membership action&#x3D;exclude, or bundle_upsert variation_mode&#x3D;detach for a manual bundle).  Call bundle_list first if you need bundle names/ids beyond what&#39;s returned here. |
| [**bundleList()**](WriteApi.md#bundleList) | **POST** /api/v1/tools/bundle_list | List a tenant&#39;s bundle deals (\&quot;mix &amp; match\&quot; — e.g. \&quot;buy 4 for $77\&quot;) with id, name, trigger quantity, discount type/value, active state, schedule window, and attached variation count. Bundles are the live cart engine (MixMatchService): a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations. Always call this before bundle_upsert to get the bundle id and confirm the current discount configuration.  IMPORTANT: this reads the &#x60;bundles&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy &#x60;mix_match_rules&#x60; tenant setting that promotion_audit / product_inspect surface; those are stale display-only data. Trust this tool for what actually applies at checkout.  discount_type:   percent      → discount_value is a percentage 0–100, applied per unit.   fixed        → discount_value is dollars off PER UNIT.   fixed_total  → discount_value is the dollar TOTAL for the whole set (\&quot;$77 for 4\&quot;). |
| [**bundleMembership()**](WriteApi.md#bundleMembership) | **POST** /api/v1/tools/bundle_membership | Override one variation&#39;s membership in one bundle, independent of whatever a bundle&#39;s rule (GitHub #395 \&quot;smart collections\&quot;) would otherwise decide. Precedence is always excluded &gt; pinned &gt; rule — this is how you handle \&quot;this tenant is mostly automatic but this one variation needs to stay in/out no matter what the rule says\&quot;.  action:   pin       Force this variation INTO the bundle. Survives every future rule recompute. Also             clears any existing exclusion for it (a deliberate \&quot;include this\&quot; beats an earlier             \&quot;exclude this\&quot;).   unpin     Remove an explicit pin. If the bundle&#39;s rule still matches this variation, the next             recompute (which runs automatically here) puts it back as an automatic (\&quot;rule\&quot;) member;             otherwise it simply falls out.   exclude   Force this variation OUT of the bundle, immediately, even if it&#39;s currently pinned or             rule-matched. Never re-added by a rule recompute until unexcluded.   unexclude Lift an exclusion. If the rule still matches, the next recompute (run automatically             here) re-adds it as an automatic member; it is NOT automatically pinned.  pin/exclude/unpin/unexclude all work on fully-manual bundles too (a bundle with no rule) — they just directly add/remove the pivot row in that case, same as the vendor bundle picker&#39;s pin/exclude buttons.  Call bundle_list or bundle_rule_preview first to find the right bundle_id and variation_id. |
| [**bundleRulePreview()**](WriteApi.md#bundleRulePreview) | **POST** /api/v1/tools/bundle_rule_preview | Read-only. Shows what a bundle membership rule (\&quot;smart collection\&quot; — GitHub #395) would match, WITHOUT writing anything. Use this before bundle_upsert to sanity-check a rule against a live tenant&#39;s catalog rather than guessing blind.  Two ways to call it:   - Pass &#x60;bundle_id&#x60; to preview a bundle&#39;s CURRENTLY STORED rule (what recompute() would do right now).   - Pass &#x60;rules&#x60; (and no bundle_id) to preview a HYPOTHETICAL rule that hasn&#39;t been saved anywhere —     \&quot;what would this match if I set it up this way\&quot;. The rule is evaluated in-memory only. Passing both previews the inline &#x60;rules&#x60; against that bundle&#39;s tenant, ignoring its stored rule.  Each condition is {field, operator, value} — the same shape bundle_upsert&#39;s &#x60;rules&#x60; field takes:   field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status          (draft|active|archived) | tracking_type (weight|unit|matrix)   operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max]) Conditions are AND-ed. An empty rules list matches nothing (a bundle only auto-populates once it has at least one condition).  Returns the total match count plus up to 20 sample variations (id, product name, size/weight, current price) so you can eyeball whether the rule caught the right things before committing it. |
| [**bundleUpsert()**](WriteApi.md#bundleUpsert) | **POST** /api/v1/tools/bundle_upsert | Create or update a bundle deal (mix &amp; match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type &#x3D; \&quot;percent\&quot;      → discount_value is a percentage 0–100 (e.g. 20 &#x3D; 20% off each unit).   discount_type &#x3D; \&quot;fixed\&quot;        → discount_value is DOLLARS off PER UNIT (e.g. 5 &#x3D; $5 off each).   discount_type &#x3D; \&quot;fixed_total\&quot;  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity&#x3D;4, discount_value&#x3D;77 → \&quot;any 4 for $77\&quot;).   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \&quot;replace\&quot; (default) sets membership to exactly variation_ids; \&quot;add\&quot; attaches them to the   existing set; \&quot;detach\&quot; removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass null/omit   for an always-on bundle.  RULES (smart collections — GitHub #395):   Pass &#x60;rules&#x60; to turn this bundle into a rule-based (\&quot;smart collection\&quot;) bundle: membership is   computed automatically from a list of AND-ed conditions instead of a fixed variation_ids list.   Each condition is {field, operator, value}:     field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status            (draft|active|archived) | tracking_type (weight|unit|matrix)     operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max])   On save, membership is recomputed immediately: variations matching every condition are added as   \&quot;rule\&quot; members. Pinned members and explicit exclusions (see bundle_membership) are never touched   or overridden by a rule — excluded beats pinned beats rule. The response reports how many   variations were added/removed by this recompute.   ALWAYS call bundle_rule_preview with your intended rules BEFORE saving them here — it shows   exactly what would match without writing anything, so you don&#39;t fire a rule blind against a   live tenant&#39;s catalog.   Passing &#x60;rules: []&#x60; or &#x60;rules: null&#x60; explicitly clears the bundle back to fully manual: existing   membership is left as-is (nothing is auto-removed), but it stops auto-updating and variation_ids   takes over again.   Omit &#x60;rules&#x60; entirely to leave a bundle&#39;s current rule state (manual or automatic) untouched.  Always call bundle_list first to get the bundle_id and confirm the current configuration. |
| [**campaignApplyTemplate()**](WriteApi.md#campaignApplyTemplate) | **POST** /api/v1/tools/campaign_apply_template | Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \&quot;Choose template\&quot; action in the vendor admin.  The template is rendered with the tenant&#39;s own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign&#39;s html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \&quot;Your headline here\&quot;, etc.) — after applying, call campaign_get, then campaign_upsert to swap placeholder copy/prices inside the existing html_body (do NOT replace the whole body — that deletes the template layout). Use campaign_set_image for product photos. Or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_get + campaign_upsert (edit copy in place) + campaign_set_image (photos) → campaign_send_test (preview). |
| [**campaignControl()**](WriteApi.md#campaignControl) | **POST** /api/v1/tools/campaign_control | Pause or resume a vendor email/SMS campaign.  pause: sets status&#x3D;paused with paused_reason&#x3D;manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first. |
| [**campaignFlyerStylePack()**](WriteApi.md#campaignFlyerStylePack) | **POST** /api/v1/tools/campaign_flyer_style_pack | Teach campaign_generate_flyer what this shop&#39;s flyers look like, using their OWN past work.  A style pack is two things, both stored on the shop itself:   - Images: one or more past flyers or mood boards, held in the shop&#39;s media library.   - Art direction: short lines describing the look to carry forward (\&quot;seasonal scenes, deal-dense     grids, deep green and gold, calm lower third\&quot;).  When a pack is saved, every flyer generation attaches it ahead of the product photos and asks the model to match the MOOD and COMPOSITION — never to copy text, logos or prices out of it.  ACTIONS:   - show  — what is saved right now: the direction lines and the images on file.   - set   — save direction lines and/or point the pack at media library images.             prompt_lines replaces the saved direction. media_ids MOVES those library assets into             the style-reference folder (they stay ordinary library assets, reusable elsewhere).   - clear — remove the direction lines, and with clear_images&#x3D;true also take the images back out             of the style-reference folder. Nothing is deleted from the library.  HOW TO GET AN IMAGE IN: upload the flyer through the shop&#39;s media library (or send it to Dabby in chat, which files it there), then pass its media_id here. The pack lives entirely in the database, so it survives every deploy and can be changed at any time without one. |
| [**campaignGenerateFlyer()**](WriteApi.md#campaignGenerateFlyer) | **POST** /api/v1/tools/campaign_generate_flyer | Generate BESPOKE flyer artwork for a campaign from the shop&#39;s own product photos, then drop it straight into the campaign design. This is the intended way to make a marketing email look like a designed flyer instead of a text layout — reach for it before writing copy, because the rest of the email is built to sit under the artwork.  WHAT IT PRODUCES:   A layered, dimensional flyer illustration in the shop&#39;s brand colours, composed around the real   packaging from the product photos you name. It renders NO text — the email supplies every word   as real text so it stays readable, translatable, and safe with images disabled.  REQUIREMENTS:   The shop must have its own AI key connected (Settings → Dab AI). Generation is billed to that   key, not to the platform, so a shop without one gets a clear message instead of a flyer. Use   the highest-quality model available on the key; that is the default and it is the point.   A shop that has saved a style pack (campaign_flyer_style_pack) automatically gets its own past   flyers attached as references, so the artwork matches the creative it already sends.  HOW TO USE IT:   - theme        — what the flyer is for, in plain words (\&quot;September restock, cosy autumn mood\&quot;).   - product_ids  — the products to feature. Omit and it picks the shop&#39;s newest featured products                    that actually have photos.   - campaign_id  — a DRAFT campaign to place the artwork into. Omit to just add it to the media                    library and place it later with campaign_set_image.   - slot_index   — which image slot to fill (1-based). Defaults to the first, which is the hero.   - reference_media_ids — if the vendor just attached a past flyer (or any image) to THIS chat and                    asked for something \&quot;in the same style\&quot; or \&quot;like this one\&quot;, pass its media_id                    here. The conversation shows it as \&quot;[Library image saved: media_id&#x3D;N, ...]\&quot;                    right after the attachment. This is a one-off style/composition reference for                    this generation only — it steers mood, layout and palette, it is never                    reproduced verbatim, and it is not saved for next time. Never ask the vendor                    for a URL or to re-upload when their image is already attached to the chat —                    read its media_id from the conversation and pass it here instead. For a shop                    that wants the same look on every flyer going forward, save it once with                    campaign_flyer_style_pack instead of repeating reference_media_ids.  Typical flow: campaign_upsert (create) → campaign_apply_template (design) → campaign_upsert (real copy) → campaign_generate_flyer (hero artwork LAST) → campaign_send_test. Generate the artwork only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after the artwork is placed erases it.  The result always lands in the media library, so it can be reused on the storefront or in a later send without generating twice. |
| [**campaignLaunch()**](WriteApi.md#campaignLaunch) | **POST** /api/v1/tools/campaign_launch | Start, schedule, or cancel a vendor campaign after it is ready.  ACTIONS:   send_now — draft → materialize + send (or resume paused). Requires spam/inbox gate.   schedule — draft → scheduled_at (tenant timezone local datetime string).   cancel — sending/paused/recovering/scheduled/failed → cancelled.  dry_run defaults TRUE. Applying requires dry_run&#x3D;false AND confirm&#x3D;true. Always campaign_spam_score / campaign_send_test first, then confirm with the vendor. |
| [**campaignRecipientsRequeue()**](WriteApi.md#campaignRecipientsRequeue) | **POST** /api/v1/tools/campaign_recipients_requeue | Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \&quot;sending\&quot; rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors&#x3D;true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run&#x3D;false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor. |
| [**campaignSendTest()**](WriteApi.md#campaignSendTest) | **POST** /api/v1/tools/campaign_send_test | Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email: routes by campaign mode —   smtp → tenant SMTP (requires complete SMTP settings)   mailgun → Mailgun Messages API via a Ready sending name on the campaign   webhook → posts a test payload to the campaign webhook (no inbox email) Pass to_email for smtp/mailgun. Webhook mode uses to_email only as the sample recipient identity in the payload.  Text (SMS): Twilio. Pass to_phone. Requires Twilio connected.  Preview only — does NOT start the campaign, touch the audience, or record stats. |
| [**campaignSetImage()**](WriteApi.md#campaignSetImage) | **POST** /api/v1/tools/campaign_set_image | Drop an image from the tenant&#39;s media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant&#39;s own library) and it fills an image placeholder in the campaign body with that image, using the library image&#39;s own public URL and alt text. The URL is on the tenant&#39;s public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \&quot;Add image\&quot;. By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_upsert (real copy) → campaign_set_image (fill each placeholder LAST) → campaign_send_test. Fill placeholders only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after an image is placed erases it. |
| [**campaignUpsert()**](WriteApi.md#campaignUpsert) | **POST** /api/v1/tools/campaign_upsert | Create or edit a vendor email/SMS campaign (vendor → their customers; NOT platform drip-to-leads). Completed campaigns cannot be edited.  CREATE (no campaign_id): draft. Email needs name+subject (html_body optional — then campaign_apply_template). SMS needs name+sms_body and connected Twilio.  UPDATE (campaign_id): only passed fields change. Drafts accept all fields. Sending/paused accept content only (email: name/subject/html_body/plain_body/fallback_first_name; SMS: name/sms_body/fallback_first_name).  TRANSPORT (email): mode &#x3D; smtp | webhook | mailgun.   mailgun: require Ready mailgun_domain_ids (from tenant_mailgun_manage show); optional   mailgun_pace drip|blast.   webhook: prefer saved_webhook_id, or inline webhook_url (+ optional headers/health). AUDIENCE: contact_list_ids (manual and/or Smart Lists) and/or audience_includes_customers (default true). Lead lists cannot use smtp — use mailgun or webhook. SMS may attach Smart Lists only (not imported manual lists). Discover lists with contact_list_manage / smart_list_manage.  Optional: throttle_per_minute/hour, send_interval_minutes (1–30), send_window_*.  Does not start a live send — use campaign_send_test, then campaign_launch (confirm) or the vendor admin Send button.  ORDERING: html_body is a full replace. A table-based / already-designed body is kept exactly as sent — product and category photos from the previous version are NOT re-prepended as hero banners. Bare/fragment copy updates still carry previously appended flyer artwork (campaign_generate_flyer / campaign_set_image) so a rewrite does not wipe the art. Pass html_body \&quot;\&quot; to clear the body on purpose. |
| [**catalogCollapse()**](WriteApi.md#catalogCollapse) | **POST** /api/v1/tools/catalog_collapse | Merges a group of size-split products into ONE product with size options.  Turns \&quot;Blue Dream - 3.5G\&quot;, \&quot;Blue Dream - 7G\&quot;, \&quot;Blue Dream - 28G\&quot; (three separate products, each with one \&quot;Default\&quot; option) into a single \&quot;Blue Dream\&quot; product that sells by weight with 3.5g / 7g / 28g options. Stock is added together into one shared pool, and each old price becomes the price of its size.  Run catalog_flattening_audit first to see the groups and their exact base_name.  SAFETY: - dry_run defaults to TRUE. Nothing changes until you pass dry_run&#x3D;false. The dry run returns   the exact sizes, prices, stock, and which products would be removed. - Past orders are never affected — they keep the product name and price the customer saw. - Refused when two products in the group claim the same size, or when any product belongs to   another store. - This removes the duplicate products. It cannot be undone from here. |
| [**catalogImport()**](WriteApi.md#catalogImport) | **POST** /api/v1/tools/catalog_import | Bulk-create categories and products in one call, for onboarding a store from a scraped or exported catalog. Built on category_manage, product_manage, media_upload and product_image_assign, so results match creating each item by hand.  Pass categories (name, optional parent name) and/or products (name, price in dollars, optional sku, stock_quantity, description, category names, image_url, or a complete expected_variation_count plus named variations list). Categories are matched by name (existing ones are reused, never duplicated); products are matched by exact name (existing ones are skipped, never overwritten). Up to 100 products per call.  dry_run defaults true: the first call reports what would be created and skipped. Pass dry_run&#x3D;false to write. Image failures never block a product; they are listed per item. Tiered pricing, bundles and strain-specific data are set afterwards with pricing_structure_*, bundle_* and product_manage. |
| [**catalogSaleManage()**](WriteApi.md#catalogSaleManage) | **POST** /api/v1/tools/catalog_sale_manage | Apply or remove a category shelf sale. The percent or dollar amount comes off the Was price (the compare-at price when the item is already on sale, otherwise the current price), never off the current sale price. Each changed variation&#39;s selling price and Was price are remembered. remove with mode \&quot;restore\&quot; writes both back. mode \&quot;keep\&quot; leaves the new price and drops the Was badge, forgetting the previous sale.  Selected categories include their subcategories. Products in other categories stay untouched. One category sale at a time, and it cannot run during a storewide sale. A storewide sale covers the whole catalog and discounts the current selling price instead.  This is not a coupon. coupon_upsert only discounts the current cart price at checkout and does not change or remember shelf prices.  ACTIONS:   show (default): whether a category sale is active and how many prices it remembers.   apply: type \&quot;percent\&quot; (0-100) or \&quot;flat\&quot; (dollars), amount, and category_ids.   remove: mode \&quot;restore\&quot; (default) or \&quot;keep\&quot;.  apply and remove preview unless dry_run&#x3D;false and confirm&#x3D;true. Resolve category ids with category_manage before applying. |
| [**catalogVariantCompact()**](WriteApi.md#catalogVariantCompact) | **POST** /api/v1/tools/catalog_variant_compact | Safely merges explicit same-family unit products into one product with named variants. Preview first; live mode preserves the survivor, moves variations, unions categories, records old-slug redirects, and removes absorbed products atomically. |
| [**categoryManage()**](WriteApi.md#categoryManage) | **POST** /api/v1/tools/category_manage | List, create, update, or delete a tenant&#39;s storefront categories.  ACTIONS:   list   (default): return every category with id, name, slug, parent, sort_order,          is_active, is_featured, and image_url. Always call this first to find a          category_id before update/delete, and to check for slug collisions before create.   create: requires name (slug is auto-generated from name if omitted).   update: requires category_id. Only the fields you pass are changed.   delete: requires category_id and confirm&#x3D;true. Refuses if the category still has          products or children attached (detach or reassign them first).  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites branding onto). Omit both to leave images untouched.  Always call action&#x3D;list first to confirm category_id / slug before update or delete. |
| [**contactCleanup()**](WriteApi.md#contactCleanup) | **POST** /api/v1/tools/contact_cleanup | Removes contacts that failed email validation from an imported list.  Typical use: a CSV import brought in thousands of addresses, the validator graded them, and the list is now full of dead addresses that will bounce and hurt sending reputation.  SAFETY: - dry_run defaults to TRUE. Nothing is deleted until you pass dry_run&#x3D;false. The dry run   returns exactly how many contacts match and a sample of them. - Only statuses \&quot;invalid\&quot; and \&quot;risky\&quot; can be purged. \&quot;unknown\&quot; means the validator has not   graded that contact YET — those are never deleted, and asking for them is refused. - Scoped to one contact list when list_id is given, otherwise the whole store. - Contacts are deleted, not unsubscribed. This cannot be undone from here. |
| [**contactListManage()**](WriteApi.md#contactListManage) | **POST** /api/v1/tools/contact_list_manage | Discover and manage contact lists (manual + Smart) for campaigns, coupons, and Klaviyo.  ACTIONS:   list (default): all lists with id, name, kind (manual|smart), default_type, contacts_count,     usable_for hints (email/sms/coupon/klaviyo).   show: one list by list_id (includes Smart List definition when kind&#x3D;smart).   create_manual: create an empty imported/manual list (name required; default_type lead|customer).   rename: update name/description/default_type on a manual list (not Smart — use smart_list_manage).   delete: remove a list (dry_run default true; applying needs dry_run&#x3D;false + confirm&#x3D;true).  CSV import stays in the vendor admin. Use smart_list_manage for Smart List CRUD/preview. |
| [**couponUpsert()**](WriteApi.md#couponUpsert) | **POST** /api/v1/tools/coupon_upsert | Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those). A coupon discounts the current cart price. It does not change the shelf price or the Was price, and it cannot put an earlier sale back. For a category shelf sale off the Was price that remembers and restores the previous sale, use catalog_sale_manage.  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type &#x3D; \&quot;percentage\&quot;     → value is a percentage 0-100 off the order subtotal.   type &#x3D; \&quot;fixed\&quot;          → value is DOLLARS off the order subtotal (e.g. 10 &#x3D; $10 off).   type &#x3D; \&quot;free_delivery\&quot;  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \&quot;code-triggered freebie\&quot;: a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\&quot;email\&quot;|\&quot;phone\&quot;|\&quot;both\&quot;) controls how max_uses_per_customer is enforced. Using \&quot;phone\&quot; or \&quot;both\&quot; REQUIRES the tenant&#39;s \&quot;Require phone at checkout\&quot; setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to&#x3D;\&quot;products\&quot; or \&quot;categories\&quot; restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to&#x3D;\&quot;products\&quot;) or category ids (when   applies_to&#x3D;\&quot;categories\&quot;) — all ids must belong to this tenant. Passing applies_to&#x3D;\&quot;products\&quot;/   \&quot;categories\&quot; with an empty or omitted applies_to_ids behaves the same as \&quot;all\&quot; (no scope   configured yet). Switching back to applies_to&#x3D;\&quot;all\&quot; does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids&#x3D;[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \&quot;mounting ladder\&quot; so its discount climbs with each successive order a   customer&#39;s delivery subscription generates, pegging at a ceiling — e.g. order 1 &#x3D; 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  contact_list_id optionally restricts redemption to a tenant-owned manual or Smart List. Smart Lists are re-evaluated live; manual-list customers and leads are matched by contact email. The restriction is checked at cart apply and checkout redemption time. Pass null to remove it.  Always call coupon_list first to get the coupon_id and confirm the current configuration. |
| [**customerBlockManage()**](WriteApi.md#customerBlockManage) | **POST** /api/v1/tools/customer_block_manage | Block or unblock a customer&#39;s current email and/or phone using existing tenant Security lists. Preview by default. Apply only on explicit user instruction with dry_run&#x3D;false, confirm&#x3D;true, a reason and expected_snapshot from status/preview. Unblocking removes the selected shared identifier blocks, including pre-existing ones; affects anyone using them. Other restrictions remain. Does not delete customers, change orders or create an account-ID ban. |
| [**customerMessageDraftsCreate()**](WriteApi.md#customerMessageDraftsCreate) | **POST** /api/v1/tools/customer_message_drafts_create | Create reply drafts only when the user explicitly asks in their current message to draft or prepare customer replies. Never call merely because unanswered messages exist. This tool never sends messages; Dabby presents each original customer message and proposed reply in chat for explicit confirmation. |
| [**customerUpdate()**](WriteApi.md#customerUpdate) | **POST** /api/v1/tools/customer_update | Update a customer&#39;s contact fields (name, email, phone) and/or suppress marketing consent (email_opt_out, sms_marketing_opt_out, sms_notifications_muted — one-way, cannot un-suppress). Verification, loyalty, and other DabDash-owned fields cannot be set here. |
| [**deliveryDelayMessageDraftsCreate()**](WriteApi.md#deliveryDelayMessageDraftsCreate) | **POST** /api/v1/tools/delivery_delay_message_drafts_create | Create delivery-delay email drafts only when the user explicitly asks in their current message to prepare or send a delivery update. Use only after order status confirms the customer is out for delivery. This tool never sends email; Dabby presents each proposal in chat for explicit confirmation. |
| [**deliverySettingsManage()**](WriteApi.md#deliverySettingsManage) | **POST** /api/v1/tools/delivery_settings_manage | Show or configure tenant delivery operations: logistics gate, driver self-serve, customer tracking visibility, proof of delivery, driver notification suppressions, and auto-dispatch. Set uses dry_run&#x3D;true by default and writes are audited. Enable delivery logistics before enabling dependent driver controls. WhatsApp is not changed by this tool. |
| [**freebieUpsert()**](WriteApi.md#freebieUpsert) | **POST** /api/v1/tools/freebie_upsert | Create or update a freebie rule (\&quot;spend $X, get a free item\&quot;) on behalf of a tenant. Freebies are evaluated on every cart change: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart (distinct from bundles, which fire on cart CONTENTS/quantity — use bundle_upsert for those).  UPDATE MODE (freebie_id provided):   Edits the freebie. Only the fields you pass are changed; omitted fields are left as-is,   EXCEPT category_ids, which — like bundle_upsert&#39;s variation_ids — fully replaces the category   set whenever passed (pass an empty array to clear all categories).  CREATE MODE (no freebie_id):   Creates a new freebie. name, product_id, spend_threshold, and quantity are required.  product_id and variation_id (if given) MUST belong to the same tenant — foreign ids are rejected, not silently ignored (unlike bundle_upsert&#39;s variation_ids, since a freebie needs exactly one product to give away, not a set).  spend_threshold is entered in DOLLARS (e.g. 50 for a $50 minimum spend) — the tool stores cents internally.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order.   false  → this rule cannot combine with other freebie rules.   Does NOT multiply this rule&#39;s own quantity by how many multiples of spend_threshold the cart   reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on freebie.  Always call freebie_list first to get the freebie_id and confirm the current configuration. |
| [**loyaltyManage()**](WriteApi.md#loyaltyManage) | **POST** /api/v1/tools/loyalty_manage | Show or configure a store&#39;s points-based loyalty program, including vendor-defined lifetime-point tiers with automatic percentage discounts.  ACTIONS:   show (default): current settings.   set: pass only what changes. dry_run defaults true; pass dry_run&#x3D;false to save.   set_customer_lifetime: set one explicitly sourced customer&#39;s lifetime total. This is     for a one-time migration and is dry-run by default; it never changes spendable balance. |
| [**mediaCompose()**](WriteApi.md#mediaCompose) | **POST** /api/v1/tools/media_compose | Build a finished campaign/storefront creative ON THE SERVER: take a base picture, drop the vendor&#39;s logo on top, add a headline and subtitle, and save the RESULT straight into the tenant&#39;s media library. Returns a media_id + public URL ready to place with campaign_set_image, category_manage, widget_manage, tenant_branding_manage, product_image_assign, or tenant_blog_upsert.  PROVIDE THE BASE (exactly one):   - base_media_id — preferred for tenant-owned photos already in the library (media_list /     media_upload). Keeps the source visible in Admin → Media.   - base_url      — public https URL for external/generated artwork. Only the composed     OUTPUT is saved as a library row; the raw URL bytes are not separately ingested. Prefer     media_upload → base_media_id when the photo should remain in the media library (#184).  PROVIDE THE LOGO (optional, at most one):   - logo_media_id / logo_url — same two options as the base (prefer logo_media_id for the     tenant logo so it stays linked in Media).   THE LOGO IS NEVER ALTERED. It is scaled to fit (aspect ratio locked) and placed as-is —   its colours, strokes and transparency are left exactly as supplied. For legibility on busy   artwork use the &#x60;scrim&#x60; option, which darkens the area BEHIND the logo and text.  LAYOUT: logo on top, headline under it, subtitle under that — the block is centred horizontally and positioned with logo_position (top / center / bottom). Sizes are given as a fraction of the base image width so they scale with any canvas. Long text is word-wrapped automatically; the response&#39;s &#x60;wrapped&#x60; flag tells you when that happened so you can shorten it.  CONSTRAINTS: sources max 5 MB, JPEG/PNG/WebP/GIF, max 8000x8000. The finished image goes through the same pipeline as media_upload — re-encoded to WebP, resized to fit, de-duplicated by content — so identical inputs return the existing media_id instead of a duplicate.  Typical flow: media_upload (or media_list) → media_compose → place with media_id.  STOREFRONT SWAG MODE (category_id or widget_id): instead of the manual layout above, pass category_id or widget_id to run the platform&#39;s own storefront branding pipeline (SwagImagesService) against that exact category or widget — the same compositor used by the dev seeder and vendor admin: tenant logo eyebrow, Bebas Neue headline (shrunk to fit long names automatically), brand-colour accent rule, and a sub-label (the tenant&#39;s live domain for categories, the widget&#39;s own subtitle for widgets). Pass base_media_id or base_url to set/replace the row&#39;s base_image_path first; omit both to recompose from whatever base_image_path is already saved. Saves directly to image_path on that category or widget — this is an internal/admin tool, not exposed to vendors. |
| [**mediaList()**](WriteApi.md#mediaList) | **POST** /api/v1/tools/media_list | List the images in a tenant&#39;s media library — id, public URL, dimensions, filename, folder, and alt text. Use this to find the media_id of an image to place into a campaign with campaign_set_image, instead of guessing. Returns newest first.  Filter with &#x60;folder&#x60; (exact match) or &#x60;search&#x60; (filename substring). &#x60;visibility&#x60; defaults to \&quot;public\&quot; (the emailable assets); pass \&quot;all\&quot; or \&quot;private\&quot; to widen. Confirm the tenant_slug with tenant_list first. |
| [**mediaUpload()**](WriteApi.md#mediaUpload) | **POST** /api/v1/tools/media_upload | Upload an image into a tenant&#39;s media library (the same library the vendor admin uses). The image is ingested through the platform&#39;s shared media pipeline — re-encoded to WebP, resized to fit, EXIF- oriented, content-addressed for dedup — and a media_assets row is created. It ALWAYS lands in the library, and the tool returns the new media_id and public URL.  PLACE the media_id with the matching tool (never hotlink the URL as a product/blog/hero path):   - campaign_set_image — email campaign HTML   - category_manage / widget_manage — storefront images (media_id / base_media_id)   - tenant_branding_manage — store logo (media_id) or homepage hero (hero_media_id)   - product_image_assign — product featured / gallery   - tenant_blog_upsert — vendor blog featured image   - media_compose — base_media_id / logo_media_id for composed creatives  PROVIDE THE IMAGE ONE OF THREE WAYS (exactly one):   - source_url    — fetch the image from a public http(s) URL (preferred for remote agents).   - source_path   — read a local file path on the server/agent host (often broken on prod FS).   - source_base64 — raw base64 of the image bytes (no data: prefix needed; a data: prefix is stripped).  CONSTRAINTS: max 5 MB; JPEG, PNG, WebP, or GIF. Identical bytes already in the library are de-duped (you get the existing media_id back). Uploads default to public visibility so the image is emailable.  Optional alt_text and folder help organise and caption the asset. Confirm the tenant_slug with tenant_list first. To see what&#39;s already in the library, use media_list. |
| [**notificationSettingsManage()**](WriteApi.md#notificationSettingsManage) | **POST** /api/v1/tools/notification_settings_manage | Show or configure tenant-wide vendor order, low-stock, digest, restock, and storefront enquiry notification preferences. This tool deliberately excludes per-user opt-outs, SMS provider configuration, and WhatsApp. Set uses dry_run&#x3D;true by default and writes are audited. |
| [**orderUpsert()**](WriteApi.md#orderUpsert) | **POST** /api/v1/tools/order_upsert | Upsert WooCommerce orders into this tenant (idempotent WP-{id} order numbers). Used by dabdash-for-woocommerce continuous order sync and small residual imports. Pass at most 100 rows matching the wordpress_commerce_import orders shape. Preview defaults on; applying requires dry_run&#x3D;false and confirm&#x3D;true. |
| [**pricingStructureAssign()**](WriteApi.md#pricingStructureAssign) | **POST** /api/v1/tools/pricing_structure_assign | Assign a shared bundle pricing structure to one or more products. Re-syncs variations for each reassigned product. Automatically deletes orphaned inline (hidden) structures when replacing them.  SAFETY RULES enforced by this tool: - The target structure_id must be a BUNDLE (is_hidden&#x3D;false). Inline structures cannot be assigned   to products this way — that would violate the 1:1 contract. - If a product&#39;s current structure is inline (hidden) and this product is its only consumer   (product_count &#x3D;&#x3D; 1), the old inline structure is deleted automatically. - If a product&#39;s current structure is inline but product_count &gt; 1, assignment is refused for that   product with an explanation — this is a data anomaly that needs manual resolution. - Each product result includes a status: assigned | skipped (already on this structure) | refused.  Use pricing_structure_list to get valid structure IDs before calling this tool. |
| [**pricingStructureDelete()**](WriteApi.md#pricingStructureDelete) | **POST** /api/v1/tools/pricing_structure_delete | Delete one or more pricing structures by ID.  SAFETY RULES enforced by this tool: - BUNDLE structures (is_hidden&#x3D;false): always deletable unless products are still assigned.   Pass force&#x3D;true to delete even when products are assigned (use only after migrating them). - INLINE structures (is_hidden&#x3D;true): only deletable when product_count&#x3D;0 (orphaned).   Inline structures with products attached cannot be deleted — use pricing_structure_assign   to move the product to a bundle first, which auto-cleans the inline structure. |
| [**pricingStructureList()**](WriteApi.md#pricingStructureList) | **POST** /api/v1/tools/pricing_structure_list | List all pricing structures for a tenant with their kind (inline|bundle), product count, tracking type, and tier summary. Always call this before pricing_structure_upsert or pricing_structure_assign to get structure IDs and confirm which structures are bundles vs inline (1:1 product) structures.  kind&#x3D;inline  → hidden 1:1 structure tied to exactly one product (is_hidden&#x3D;true) kind&#x3D;bundle  → shared structure visible on /admin/pricing, used by 0 or more products (is_hidden&#x3D;false) |
| [**pricingStructureRestore()**](WriteApi.md#pricingStructureRestore) | **POST** /api/v1/tools/pricing_structure_restore | Surgical restore tool. Rebuilds a single product&#39;s pricing structure and variations EXACTLY to a specified state. Bypasses the standard syncVariationsForProduct routine — you control every field.  Use this AFTER inventory_audit_lookup has revealed the pre-incident state of variations whose names/prices/stock were destroyed by an erroneous bundle reassignment.  Behaviour: - Creates a NEW inline (hidden, 1:1) pricing structure with the given tracking_type, tier   definitions, and a name like \&quot;Product: &lt;product_name&gt; (Hidden)\&quot;. Old structure linkage is   replaced. The previous structure is NOT deleted by this tool. - For each tier in the spec, finds-or-creates a variation. Matching is by &#x60;restore_variation_id&#x60;   if provided, else by name. If found, the variation is updated in place (preserving its id and   its audit-log history). If not found, a new variation is created. - Stock_quantity is set EXACTLY to the value specified — this is the whole point of the tool. - Sets product.tracking_type, product.inventory_mode, product.base_unit per the new structure. - Variations on the product not referenced by any tier in the spec are DEACTIVATED (is_active&#x3D;false)   so they stop being shown but their audit history is retained. Pass &#x60;delete_unreferenced&#x3D;true&#x60; to   hard-delete them instead.  SAFETY: - Wrap each call in its own transaction. - Will refuse if the new tracking_type is incompatible with stored cost data. - Inline-only by design — bundles are not recreated by this tool. Use pricing_structure_assign   to put the product on a bundle if that&#39;s what you want. |
| [**pricingStructureUpsert()**](WriteApi.md#pricingStructureUpsert) | **POST** /api/v1/tools/pricing_structure_upsert | Create or edit a pricing structure&#39;s tiers, name, and tracking type. Operates in three modes:  BUNDLE MODE (structure_id provided, structure is not hidden):   Edit a shared bundle structure visible on /admin/pricing. Tiers are replaced and ALL products   linked to the bundle are re-synced. Returns how many products were affected as a warning.  INLINE MODE (product_slug or product_id provided, no structure_id):   Edit the hidden 1:1 pricing structure for a single product. Tiers are replaced and variations   are re-synced for that product only. Refuses if the product currently uses a bundle structure —   use pricing_structure_assign to detach from the bundle first.  CREATE BUNDLE MODE (no structure_id, no product_slug/product_id):   Create a new shared bundle structure. Does not link it to any products.  SAFETY RULES enforced by this tool: - Never accepts structure_id pointing to a hidden (inline) structure — always go via product_slug/product_id. - Tiers for weight/matrix types must have weight_grams &gt; 0. - Tiers for simple type: only the first tier is used; name and weight_grams are normalised. - Prices are accepted as dollar amounts (e.g. 12.99) and converted to cents internally. |
| [**productFeatureManage()**](WriteApi.md#productFeatureManage) | **POST** /api/v1/tools/product_feature_manage | List or set which products appear in a tenant&#39;s curated storefront home grids (Classic and Modern). Each section shows up to 8 products, ordered by the tenant&#39;s default product sort, and hides itself when empty.  SECTIONS (pass as &#x60;section&#x60;):   featured (default) → is_featured → \&quot;Featured Products\&quot;   deals              → is_featured_deal → \&quot;Featured Deals\&quot;   new                → is_featured_new → \&quot;Featured New Inventory\&quot;  Optional schedule windows (starts_at / ends_at) are tenant-local calendar dates stored as UTC. Null start &#x3D; live now; null end &#x3D; never expires. Storefront membership is query-time (flag on + within window).  This does not change the Modern hero&#39;s optional spotlight card; use storefront_theme_manage or store_settings_manage (hero_spotlight_mode / hero_spotlight_product) for that.  ACTIONS:   list (default): return every product flagged for the chosen section          (including scheduled/ended) with id, slug, name, stock_status,          the three featured flags, schedule window, and status          (live|scheduled|ended).   set: pass product_ids (array) and featured (bool) to set the section&#39;s          flag on those products. Optional starts_at / ends_at apply when          featuring; turning featured off clears that section&#39;s window.          Unlisted products are left untouched.  Products must be resolved to ids first (product_inspect or this tool&#39;s list action). Featuring an out-of-stock product is allowed but usually undesirable — check stock_status in the list output before featuring. |
| [**productImageAssign()**](WriteApi.md#productImageAssign) | **POST** /api/v1/tools/product_image_assign | Set a product&#39;s featured image and/or gallery from the tenant media library. Pass media_id (from media_list / media_upload) for featured_image, and/or gallery_media_ids to replace gallery_images. Does not upload bytes and does not create media_assets rows — ingest first with media_upload. Assets must be public (private library items have no storefront URL).  dry_run (default true) previews without writing. overwrite (default false) refuses to replace an existing featured_image unless true. clear_featured clears featured_image without setting a new one.  Distinct from product_image_strain_assign, which writes a shared platform strain CDN URL and never creates a tenant library row (GitHub #72 / #184). |
| [**productImageStrainAssign()**](WriteApi.md#productImageStrainAssign) | **POST** /api/v1/tools/product_image_strain_assign | Apply a hosted platform strain photo to a product&#39;s featured_image. Writes a URL string to the product row only — never uploads/copies bytes, never creates a tenant media_assets row (this is the shared strain library, not tenant media).  Two modes (exactly one required):   assignments: explicit [{product_id, strain_id}, ...] pairs you already     decided on (e.g. from product_image_strain_match&#39;s proposals).   auto_match: true — re-runs product_image_strain_match&#39;s own matching     internally against products missing a featured image (optionally     scoped by search/limit) and applies every exact/partial match found.  dry_run (default true) previews without writing — always call once with dry_run&#x3D;true and review the results before dry_run&#x3D;false.  overwrite (default false) — a product that already has a featured_image is skipped, never replaced, unless overwrite&#x3D;true. Idempotent: if a product&#39;s featured_image already equals the target strain&#39;s hosted URL, it is reported skipped/already_set regardless of overwrite (nothing to do, not an error).  Refuses to assign a strain whose image is not hosted on the platform (skipped/strain_image_not_hosted) — never writes a dead or third-party hotlinked URL as a product&#39;s featured image. |
| [**productManage()**](WriteApi.md#productManage) | **POST** /api/v1/tools/product_manage | Create a product for a tenant. With no variations this creates a simple one-price product, matching a blank create-product page. For named options, pass the complete variations list and expected_variation_count; the product is created as unit variation inventory and the pricing tiers are kept in sync. Call strain_lookup first when the name looks like a strain, then pass strain_id so description, type, THC/CBD, indica/sativa, effects, flavors, rating, and the hosted strain photo fill in exactly like the vendor create page.  If strain_id is omitted, this tool name-matches the platform strain library itself: an exact name match is applied automatically; close matches are returned as strain_matches so you can offer them. Pass skip_strain_enrich&#x3D;true to skip that.  ACTIONS:   create: requires name. Optional price (dollars), sku, stock_quantity,           category_ids, description, strain_id, or a complete variations list. update: requires product_id. Pass any of name, description, strain_id,           category_ids, meta_title, or meta_description to groom an existing           product without changing its price.           Strain enrichment never replaces an existing product photo; use           product_image_assign with a tenant media_id for photos.           meta_title max 60 chars; meta_description max 160 chars. |
| [**productUpdateBySku()**](WriteApi.md#productUpdateBySku) | **POST** /api/v1/tools/product_update_by_sku | Update a simple product&#39;s stock quantity and/or price by SKU — the inventory-sync path for an external POS. v1 scope: SIMPLE products only (single implicit unit, no weight/variant tiers). Every other pricing type (weight, unit, matrix, matrix_unit) is rejected with a clear message; those need per-tier/per-variant targeting that a flat SKU+quantity+price payload cannot express safely. Always call product_inspect with sku first to confirm which product/type you are targeting. |
| [**productVariationSync()**](WriteApi.md#productVariationSync) | **POST** /api/v1/tools/product_variation_sync | Atomically create or reconcile the complete variation list and per-variation inventory for one product. Use this for unit or matrix products after pricing is established. Choose unit for one-dimensional named variants and matrix_unit only for a real name/flavour × quantity/pack-size grid. The payload must contain every variant, an exact expected_variation_count, price, cost_price, and stock_quantity for every row. It is idempotent: matching existing IDs or names are updated, missing rows are created, and existing rows are never deleted. dry_run defaults to true. A successful live run returns before/after counts and stock totals for verification. Weight sizes of one flavor share one gram count — write that count on each size, and never describe it as extra inventory. Pack sizes are separate stock. If the vendor gives one count for a flavor, ask which pack size it belongs to before copying it onto every size. Never split one product across multiple calls. |
| [**productVisibilityManage()**](WriteApi.md#productVisibilityManage) | **POST** /api/v1/tools/product_visibility_manage | Set whether tenant products are public or inventory-only without changing inventory, cost, price, category assignments, images, or variations.  visibility&#x3D;public maps to product status&#x3D;active. visibility&#x3D;inventory_only maps to status&#x3D;archived, which removes the product from storefront product, category, related-product, and sitemap queries while preserving the product and its inventory for vendor use. This is reversible.  dry_run defaults to true. A write requires dry_run&#x3D;false and confirm&#x3D;true. Always preview first. Only the explicitly listed product_ids are considered. |
| [**purchaseOrderDraftCreate()**](WriteApi.md#purchaseOrderDraftCreate) | **POST** /api/v1/tools/purchase_order_draft_create | Create a draft purchase order for a supplier. Does not receive stock. After creating, add lines with purchase_order_line_add, then tell the vendor to review the draft in admin (never auto-receive). |
| [**purchaseOrderLineAdd()**](WriteApi.md#purchaseOrderLineAdd) | **POST** /api/v1/tools/purchase_order_line_add | Add a product line to a draft purchase order. Pass product_id (and variation_id when the product tracks stock by size). qty is the ordered quantity. unit_cost is dollars per unit; omit to use last cost. |
| [**reviewsManage()**](WriteApi.md#reviewsManage) | **POST** /api/v1/tools/reviews_manage | Show or configure verified-purchase product reviews for a tenant, and preview or apply moderation changes to one review. Customers can review products only after a delivered order. Hidden reviews are excluded from the product rating.  ACTIONS:   show (default): current review setting and moderation counts.   set: pass enabled to turn review collection on or off. dry_run defaults true.   hide or unhide: pass review_id. dry_run defaults true; applying a moderation   change also recalculates that product&#39;s rating. |
| [**seoMaxImageManage()**](WriteApi.md#seoMaxImageManage) | **POST** /api/v1/tools/seo_max_image_manage | Manage the SEO Max product-image rollout without Artisan commands or seed data. Use action&#x3D;status first. action&#x3D;configure toggles AVIF product delivery and the AVIF-only image sitemap. action&#x3D;backfill processes active products in bounded, resumable batches; dry_run&#x3D;true only reports work. Repeat with next_cursor as after_id until it is null. action&#x3D;submit notifies Google Search Console and IndexNow after the sitemap is live. |
| [**seoSettingsManage()**](WriteApi.md#seoSettingsManage) | **POST** /api/v1/tools/seo_settings_manage | Show or update a tenant&#39;s standard SEO and SEO MAX settings. Standard fields control homepage metadata, title templates, sitemap, Open Graph, and Twitter metadata. SEO MAX fields control automated blog targeting and publishing, plus image delivery flags.  ACTIONS:   show (default): current values, including the stored featured-image canvas path.   set: pass only fields to change. dry_run defaults true; pass dry_run&#x3D;false to save. Enabling SEO MAX requires an active SEO MAX subscription or trial and complete schedule settings. Featured-image canvas uploads are handled by media tools and are read-only here. |
| [**smartListManage()**](WriteApi.md#smartListManage) | **POST** /api/v1/tools/smart_list_manage | Create, edit, preview, or delete Smart Lists (dynamic customer audiences).  ACTIONS:   list / show — discover Smart Lists (also available via contact_list_manage).   upsert — create (omit list_id) or update. Requires name + definition     {version:1, match:\&quot;all\&quot;|\&quot;any\&quot;, conditions:[...]}.     Rule types: purchased_category, recent_category_buyers, lapsed_category_buyers,     not_purchased_category, purchased_product, purchase_count, spend_total,     top_spenders, dormant_customer, loyalty_lifetime_points, loyalty_tier.   preview — count matching customers for a definition (or existing list_id).   delete — dry_run default true; applying needs dry_run&#x3D;false + confirm&#x3D;true.  Attach Smart Lists to campaigns with campaign_upsert.contact_list_ids or to coupons via coupon_upsert.contact_list_id. Project to Klaviyo with tenant_klaviyo_manage. |
| [**storeSettingsManage()**](WriteApi.md#storeSettingsManage) | **POST** /api/v1/tools/store_settings_manage | Show or update a store&#39;s core settings: display name, phone, address, coordinates, timezone, currency, country, locale, brand colours, and opening hours.  ACTIONS:   show (default): current values.   set: pass only the fields to change. dry_run defaults true — the first call        reports the diff without writing; pass dry_run&#x3D;false to save.  Brand colours: theme_color is the primary, accent_color the secondary (both #RRGGBB; a 3-digit #RGB accent is expanded). button_text_color is auto (default: white or near-black, whichever contrasts more), white, or dark. Every response includes &#x60;palette&#x60;: the effective text colour on primary/accent, its WCAG contrast ratio, and warnings for any pairing under 4.5:1 — dry-run first to preview a palette.  opening_hours is a list of {days: [\&quot;Monday\&quot;, ...], opens: \&quot;09:00\&quot;, closes: \&quot;21:00\&quot;} rows (24h HH:MM, full English day names). Passing it replaces all rows; pass an empty list to clear. Hours feed the storefront About page and search-engine markup. storefront_theme picks the storefront look: classic (default) or modern. theme_mode controls the storefront colour mode: light or dark. hero_* fields preserve the vendor&#39;s full hero message and modern-theme content. The Modern hero featured-product card is optional: hero_spotlight_mode is auto (newest sale), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. Badge, caption, and button are hero_spotlight_label, hero_spotlight_kicker, and hero_spotlight_cta. product_feature_manage only toggles the separate Featured Products grid. The same card and the rest of the Modern homepage/footer are also on storefront_theme_manage. Vendors edit the card in Settings → Theme. hero_ticker_items replaces custom ticker rows with {label, tag, url} objects; URLs must be site paths or https:// links. Use hero_ticker_mode&#x3D;custom to show them. hero_status_mode controls the Modern hero online/last-call pill: hours (from opening_hours), auto (delivery slots when available, else hours), or off. minimum_age is the storefront age gate / About compliance age (e.g. 19 in NS). footer_notes and about_* set Modern footer notes and About intro copy. privacy_url is the authoritative published privacy-policy URL. Use the exact blog URL when the policy is a blog post. delivery_meta_description is an optional, tenant-specific description for the public /delivery page. Keep it factual and 160 characters or fewer. social_facebook / social_instagram / social_twitter / social_x / google_business_url / gbp_url are absolute https URLs published as Organization and LocalBusiness sameAs (Knowledge Panel alignment). freebie_cost_in_cogs (default true) includes giveaway freebie wholesale cost in vendor COGS / margin reports even though freebie revenue is $0. revenue_attribution_date is placed (order checkout day) or delivered (scheduled delivery calendar day) for analytics revenue dating. Slug, owner, subscription, and custom domain are not changed here. |
| [**storefrontThemeManage()**](WriteApi.md#storefrontThemeManage) | **POST** /api/v1/tools/storefront_theme_manage | Show or update Modern storefront theme data: hero copy, the optional hero featured-product card, deals ticker, homepage sections, footer, and About cards.  The hero card is optional. hero_spotlight_mode is auto (newest sale product), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. product_feature_manage only toggles the separate Featured Products grid. Vendors edit the same card in Settings → Theme → Hero features.  ACTIONS:   show (default): current saved fields. Unset custom text is null and uses the     storefront&#39;s shipped fallback; toggle values include their effective defaults.   set: pass only fields to change. Omitted fields are preserved. Empty text clears     the custom value. about_cards replaces the full card list. hero_ticker_items     replaces the custom ticker.   dry_run defaults true; pass dry_run&#x3D;false to save.  The WhatsApp number is left untouched unless whatsapp_number is explicitly passed. |
| [**supplierUpsert()**](WriteApi.md#supplierUpsert) | **POST** /api/v1/tools/supplier_upsert | Create or update a product supplier for purchase orders.  UPDATE MODE (supplier_id): only the fields you pass are changed. CREATE MODE (no supplier_id): name is required. If a supplier with the same name already exists for this vendor, that row is updated instead.  Always resolve the supplier_id before purchase_order_draft_create. |
| [**suppressionManage()**](WriteApi.md#suppressionManage) | **POST** /api/v1/tools/suppression_manage | Inspect or lift email suppressions / campaign unsubscribes for a tenant.  ACTIONS:   list — recent suppressed customers (email_opt_out) and unsubscribed contacts.   lift_transactional — clear customer email_opt_out for an email.   resubscribe — restore contact.subscribed for campaign lists.   lift_all — both transactional + campaign for one email.  Mutating actions: dry_run default true; apply needs dry_run&#x3D;false + confirm&#x3D;true. |
| [**tenantBlogUpsert()**](WriteApi.md#tenantBlogUpsert) | **POST** /api/v1/tools/tenant_blog_upsert | Update an existing tenant (vendor) blog post&#39;s fields — title, excerpt, body, category, featured_image, meta_title, meta_description, og_image, or is_published. Only the fields you pass are changed. Use tenant_blog_lookup first to find the post_id or slug.  IMAGE: pass media_id (from media_list / media_upload) to set featured_image from a tenant media-library asset. Raw featured_image URLs/paths are refused — ingest with media_upload first, then place by media_id (GitHub #184). Pass featured_image empty/null to clear. A superseded image this post previously owned is cleaned up from disk automatically once the change is saved.  dry_run (default true) previews the fields that would change without writing — call once with dry_run&#x3D;true and review before dry_run&#x3D;false. |
| [**tenantBrandingManage()**](WriteApi.md#tenantBrandingManage) | **POST** /api/v1/tools/tenant_branding_manage | Show or update a tenant&#39;s store logo, homepage hero image, and BIMI email logo, then upsert the Cloudflare &#x60;default._bimi&#x60; TXT record when the zone is connected.  ACTIONS:   show (default): current logo_path, storefront logo URL (custom domain when          set), hero_image_path/URL, BIMI path/URL, the TXT value to publish,          and whether Cloudflare is connected.   set: pass media_id (from media_list / media_upload) to point logo_path at a          library asset. Pass hero_media_id for settings.hero_image_path, or          clear_hero&#x3D;true to remove the hero. Pass exactly one of source_base64 /          source_path / source_url for an SVG to store the BIMI logo at          tenants/{id}/bimi-logo.svg (media_upload cannot take SVG). dry_run          defaults true — the first call reports what would happen.  Logo URLs and BIMI &#x60;l&#x3D;&#x60; values always use the tenant storefront host (custom domain or {slug}.dabdash.com), never the platform APP_URL. |
| [**tenantDomainManage()**](WriteApi.md#tenantDomainManage) | **POST** /api/v1/tools/tenant_domain_manage | Show, connect, verify, or remove a store&#39;s custom domain (e.g. maritimesmoke.com).  ACTIONS:   show (default): current domain, verified flag, and the DNS TXT record to add.   set: connect a domain (bare hostname, no http://). Resets verification. dry_run        defaults true; pass dry_run&#x3D;false to save.   verify: checks the TXT record or the storefront domain-check URL and marks the        domain verified. Run after DNS has been changed.   remove: disconnect the custom domain. dry_run defaults true.  DNS records are not created here; show returns what to add at the registrar or in Cloudflare. Until verified the store keeps working on {slug}.dabdash.com. |
| [**tenantKlaviyoManage()**](WriteApi.md#tenantKlaviyoManage) | **POST** /api/v1/tools/tenant_klaviyo_manage | Show or manage a tenant&#39;s BYO Klaviyo integration (profiles, list sync, Smart List projection, order events). Prefer guiding vendors to Settings → Integrations.  ACTIONS:   show (default): connection status + settings (never the API key).   connect / disconnect: dry_run default true; apply needs dry_run&#x3D;false+confirm.   update_settings: default_list_id, mapped_contact_list_id, smart_list_ids,     smart_list_sync_enabled.   sync_now / refresh_smart_lists / use_for_orders / available_lists. |
| [**tenantMailgunManage()**](WriteApi.md#tenantMailgunManage) | **POST** /api/v1/tools/tenant_mailgun_manage | Show or manage a tenant&#39;s Mailgun Send Email setup (campaigns + hybrid SMTP).  Prefer guiding vendors to Settings → Domain &amp; Email → Send Email (/admin/settings/domain#send-email). That is the canonical setup home — not Integrations alone.  ACTIONS:   show (default): connection status, Ready sending names, SMTP bootstrap     flag, and the #send-email setup URL. Never returns the API key.   connect: validate + store a Mailgun private key. dry_run defaults true;     applying requires dry_run&#x3D;false and confirm&#x3D;true. Dry-run still     validates the key with Mailgun (read-only).   disconnect: remove the Mailgun integration row (SMTP left alone).     Applying requires dry_run&#x3D;false and confirm&#x3D;true.   sync: import domains from the Mailgun account into local rows.   add_domain: create/reuse one sending name under the store/CF apex.   verify_domain: ask Mailgun to re-check DNS for one sending name.   autoconfigure: create mail1..mail5 under the Cloudflare zone and push DNS.     Applying requires dry_run&#x3D;false and confirm&#x3D;true.   bootstrap_smtp: fill tenants.settings.smtp from a Ready Mailgun domain.     Applying requires dry_run&#x3D;false; force_smtp&#x3D;true also needs confirm&#x3D;true.  Mutating actions default dry_run&#x3D;true; pass dry_run&#x3D;false to apply. |
| [**tenantSmtpManage()**](WriteApi.md#tenantSmtpManage) | **POST** /api/v1/tools/tenant_smtp_manage | Show or update Advanced store SMTP (manual host/user/password).  Prefer Mailgun Send Email for campaigns and easiest setup: use tenant_mailgun_manage, and send vendors to /admin/settings/domain#send-email. This tool is for Advanced / BYO SMTP only.  ACTIONS:   show (default): returns the current SMTP settings with the password omitted.   set: updates only the supplied fields. dry_run defaults true; pass dry_run&#x3D;false        to save. smtp_password is write-only and is never returned.  Keep from_address equal to the authenticated SMTP username unless the provider has explicitly authorized a different sender or alias. A successful settings update clears the tenant&#39;s stale SMTP health banner; the next send records any still-current failure. |
| [**tenantTwilioManage()**](WriteApi.md#tenantTwilioManage) | **POST** /api/v1/tools/tenant_twilio_manage | Show or manage a tenant&#39;s Twilio SMS connection (required for text campaigns). Prefer Settings → Integrations.  ACTIONS: show | connect | disconnect | test. connect needs account_sid + auth_token + from_number or messaging_service_sid. Mutating actions: dry_run default true; apply needs dry_run&#x3D;false + confirm&#x3D;true. |
| [**webhookManage()**](WriteApi.md#webhookManage) | **POST** /api/v1/tools/webhook_manage | List, create, update, or delete saved tenant webhooks (order events + campaign webhook mode).  ACTIONS: list | show | upsert | delete. upsert needs label + url (https). Optional health_url, events (order.created|order.shipped), is_active, custom_headers [{name,value}]. Signing secrets are generated server-side and returned once on create. Klaviyo-managed webhooks cannot be edited here — use tenant_klaviyo_manage. Assign to a campaign with campaign_upsert.saved_webhook_id. |
| [**widgetManage()**](WriteApi.md#widgetManage) | **POST** /api/v1/tools/widget_manage | List, create, update, or delete a tenant&#39;s homepage marketing widgets (the hero slider cards linking to a product, category, featured products, or a mix &amp; match tag).  ACTIONS:   list   (default): return every widget with id, title, subtitle, link_type, target, sort_order,          is_active, and image_url. Always call this first to find a widget_id.   create: requires title. link_type + its matching id/tag is optional but recommended so the          widget&#39;s CTA actually goes somewhere (see LINK_TYPE below). Defaults to \&quot;featured\&quot;          (no target) when omitted.   update: requires widget_id. Only the fields you pass are changed.   delete: requires widget_id and confirm&#x3D;true.  LINK_TYPE — pairs with exactly one target field:   \&quot;product\&quot;    → product_id   \&quot;category\&quot;   → category_id   \&quot;mix_match\&quot;  → mix_match_tag   \&quot;featured\&quot;   → no target needed (links to the featured-products listing)  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites the headline/logo overlay onto). Omit both to leave images untouched.  Always call action&#x3D;list first to confirm widget_id before update or delete. |
| [**zoneClone()**](WriteApi.md#zoneClone) | **POST** /api/v1/tools/zone_clone | Clone every polygon zone from a source tenant into the target tenant while preserving the target tenant&#39;s existing fee tiers. This is intended for importing a proven coverage map without importing another store&#39;s pricing.  The source zones are grouped into tiers by their minimum order, then mapped by rank to the target tenant&#39;s existing zones ordered by minimum order. The target tier&#39;s delivery_fee, service_fee, min_order, color, priority, and fulfillment_type are applied to every cloned source polygon. Existing target zones are replaced only when replace_existing&#x3D;true and confirm&#x3D;true. Zone colors use an evenly spaced spectrum from the target primary colour to its accent colour, one stop per fee rung. dry_run defaults true. |
| [**zoneManage()**](WriteApi.md#zoneManage) | **POST** /api/v1/tools/zone_manage | List or delete a tenant&#39;s delivery zones.  ACTIONS:   list   (default): return every zone with id, name, slug, is_active, is_example,          fulfillment_type, priority, delivery_fee, service_fee, min_order, and counts          of related orders/customer addresses/delivery slots. Always call this first          to find zone_ids and confirm which zones are welcome-wizard example/template          zones (is_example&#x3D;true, or slug prefixed welcome-/example-) versus real          vendor-configured coverage, before deleting anything.    delete: requires zone_ids + confirm&#x3D;true. Refuses to delete a zone that still has          orders, saved customer addresses, or delivery slots attached unless          force&#x3D;true is passed (force does not delete those related rows — it only          lets the zone row be removed; related rows keep their zone_id, which becomes          orphaned, so prefer reassigning/clearing them first). dry_run defaults true —          the first call always reports what would happen without writing.  Common use: cleaning up leftover onboarding-wizard example zones (the three \&quot;N km delivery area\&quot; circles and/or the \&quot;Downtown\&quot; demo zone, slugs welcome-5km / welcome-10km / welcome-15km / example-downtown) when a vendor ended up with both those AND real GADM administrative-boundary zones after running the wizard&#39;s circle and boundary-import steps in sequence. |
| [**zoneUpsert()**](WriteApi.md#zoneUpsert) | **POST** /api/v1/tools/zone_upsert | Create or update a delivery/pickup/shipping zone. Use zone_manage to list or delete.  CREATE (no zone_id): name is required; a zone with the same name is updated instead. UPDATE (zone_id): only the fields you pass change.  Coverage shape — pass ONE of:   polygon: list of {lat, lng} points (or [lng, lat] pairs), at least 3, at most 500.   center_lat + center_lng + radius_km: builds a 32-point circle. Good enough when            the client only publishes a list of towns; refine the polygon later. Fees and minimum order are in dollars. dry_run defaults true: the first call reports what would be saved; pass dry_run&#x3D;false to write. |


## `abandonedCartRecoveryManage()`

```php
abandonedCartRecoveryManage($abandoned_cart_recovery_manage_request): \ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManage200Response
```

Show or configure one-time abandoned-cart recovery emails for a tenant. Eligible carts are sent to an account or guest email captured during checkout; carts must be idle for the configured delay and not already recovered. Sending also requires complete tenant SMTP settings. This tool does not send immediately.  ACTIONS:   show (default): current enabled state and delay.   set: pass enabled and/or delay_hours. Omitted values are preserved.   dry_run defaults true; pass dry_run=false to save.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$abandoned_cart_recovery_manage_request = new \ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManageRequest(); // \ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManageRequest

try {
    $result = $apiInstance->abandonedCartRecoveryManage($abandoned_cart_recovery_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->abandonedCartRecoveryManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **abandoned_cart_recovery_manage_request** | [**\ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManageRequest**](../Model/AbandonedCartRecoveryManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManage200Response**](../Model/AbandonedCartRecoveryManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `blogImport()`

```php
blogImport($blog_import_request): \ShadowSoftware\DabDash\Model\BlogImport200Response
```

Create many blog posts (and the storefront's legal pages) in one call. tenant_blog_upsert can only edit an existing post; this creates new ones.  Pass posts with title and body (HTML), and optionally slug, excerpt, category, tags, meta_title, meta_description, published (default true), published_at (ISO date) and image_url (downloaded into the media library and used as the featured image). Posts are matched by slug within the store: existing ones are skipped unless update_existing=true, in which case only the fields you pass are changed.  PRIVACY: a privacy-policy post remains at its own /blog/{slug} URL. When it is the store's policy, set that exact published URL with store_settings_manage privacy_url. Terms and refund remain available at their storefront policy URLs.  dry_run defaults true: the first call reports what would be created, updated or skipped. Pass dry_run=false to write. Image failures never block a post.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$blog_import_request = new \ShadowSoftware\DabDash\Model\BlogImportRequest(); // \ShadowSoftware\DabDash\Model\BlogImportRequest

try {
    $result = $apiInstance->blogImport($blog_import_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->blogImport: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **blog_import_request** | [**\ShadowSoftware\DabDash\Model\BlogImportRequest**](../Model/BlogImportRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BlogImport200Response**](../Model/BlogImport200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleHealth()`

```php
bundleHealth($body): \ShadowSoftware\DabDash\Model\BundleHealth200Response
```

Read-only. Tenant-wide bundle health audit — the MCP equivalent of the vendor-facing /admin/bundles/health page (GitHub #395). Surfaces price-ladder conflicts: a variation currently sitting in 2+ active `fixed_total` bundles at different price points, which is exactly the \"14 variations stuck in two price rungs\" leak the original issue was about. A synchronous guard already prevents most NEW conflicts at save time, but this catches drift from bulk imports, seeders, or any write that bypassed Eloquent.  Each conflict lists the variation and every bundle it's in with that bundle's shared member price (its \"rung\"), so you can tell which bundle is now stale for that variation and remove it (via bundle_membership action=exclude, or bundle_upsert variation_mode=detach for a manual bundle).  Call bundle_list first if you need bundle names/ids beyond what's returned here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = array('key' => new \stdClass); // object

try {
    $result = $apiInstance->bundleHealth($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->bundleHealth: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **body** | **object**|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleHealth200Response**](../Model/BundleHealth200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleList()`

```php
bundleList($bundle_list_request): \ShadowSoftware\DabDash\Model\BundleList200Response
```

List a tenant's bundle deals (\"mix & match\" — e.g. \"buy 4 for $77\") with id, name, trigger quantity, discount type/value, active state, schedule window, and attached variation count. Bundles are the live cart engine (MixMatchService): a bundle fires when a cart holds at least `quantity` units across its attached variations. Always call this before bundle_upsert to get the bundle id and confirm the current discount configuration.  IMPORTANT: this reads the `bundles` table — the source of truth the storefront cart uses. It is NOT the legacy `mix_match_rules` tenant setting that promotion_audit / product_inspect surface; those are stale display-only data. Trust this tool for what actually applies at checkout.  discount_type:   percent      → discount_value is a percentage 0–100, applied per unit.   fixed        → discount_value is dollars off PER UNIT.   fixed_total  → discount_value is the dollar TOTAL for the whole set (\"$77 for 4\").

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_list_request = new \ShadowSoftware\DabDash\Model\BundleListRequest(); // \ShadowSoftware\DabDash\Model\BundleListRequest

try {
    $result = $apiInstance->bundleList($bundle_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->bundleList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_list_request** | [**\ShadowSoftware\DabDash\Model\BundleListRequest**](../Model/BundleListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleList200Response**](../Model/BundleList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleMembership()`

```php
bundleMembership($bundle_membership_request): \ShadowSoftware\DabDash\Model\BundleMembership200Response
```

Override one variation's membership in one bundle, independent of whatever a bundle's rule (GitHub #395 \"smart collections\") would otherwise decide. Precedence is always excluded > pinned > rule — this is how you handle \"this tenant is mostly automatic but this one variation needs to stay in/out no matter what the rule says\".  action:   pin       Force this variation INTO the bundle. Survives every future rule recompute. Also             clears any existing exclusion for it (a deliberate \"include this\" beats an earlier             \"exclude this\").   unpin     Remove an explicit pin. If the bundle's rule still matches this variation, the next             recompute (which runs automatically here) puts it back as an automatic (\"rule\") member;             otherwise it simply falls out.   exclude   Force this variation OUT of the bundle, immediately, even if it's currently pinned or             rule-matched. Never re-added by a rule recompute until unexcluded.   unexclude Lift an exclusion. If the rule still matches, the next recompute (run automatically             here) re-adds it as an automatic member; it is NOT automatically pinned.  pin/exclude/unpin/unexclude all work on fully-manual bundles too (a bundle with no rule) — they just directly add/remove the pivot row in that case, same as the vendor bundle picker's pin/exclude buttons.  Call bundle_list or bundle_rule_preview first to find the right bundle_id and variation_id.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_membership_request = new \ShadowSoftware\DabDash\Model\BundleMembershipRequest(); // \ShadowSoftware\DabDash\Model\BundleMembershipRequest

try {
    $result = $apiInstance->bundleMembership($bundle_membership_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->bundleMembership: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_membership_request** | [**\ShadowSoftware\DabDash\Model\BundleMembershipRequest**](../Model/BundleMembershipRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleMembership200Response**](../Model/BundleMembership200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleRulePreview()`

```php
bundleRulePreview($bundle_rule_preview_request): \ShadowSoftware\DabDash\Model\BundleRulePreview200Response
```

Read-only. Shows what a bundle membership rule (\"smart collection\" — GitHub #395) would match, WITHOUT writing anything. Use this before bundle_upsert to sanity-check a rule against a live tenant's catalog rather than guessing blind.  Two ways to call it:   - Pass `bundle_id` to preview a bundle's CURRENTLY STORED rule (what recompute() would do right now).   - Pass `rules` (and no bundle_id) to preview a HYPOTHETICAL rule that hasn't been saved anywhere —     \"what would this match if I set it up this way\". The rule is evaluated in-memory only. Passing both previews the inline `rules` against that bundle's tenant, ignoring its stored rule.  Each condition is {field, operator, value} — the same shape bundle_upsert's `rules` field takes:   field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status          (draft|active|archived) | tracking_type (weight|unit|matrix)   operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max]) Conditions are AND-ed. An empty rules list matches nothing (a bundle only auto-populates once it has at least one condition).  Returns the total match count plus up to 20 sample variations (id, product name, size/weight, current price) so you can eyeball whether the rule caught the right things before committing it.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_rule_preview_request = new \ShadowSoftware\DabDash\Model\BundleRulePreviewRequest(); // \ShadowSoftware\DabDash\Model\BundleRulePreviewRequest

try {
    $result = $apiInstance->bundleRulePreview($bundle_rule_preview_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->bundleRulePreview: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_rule_preview_request** | [**\ShadowSoftware\DabDash\Model\BundleRulePreviewRequest**](../Model/BundleRulePreviewRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleRulePreview200Response**](../Model/BundleRulePreview200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleUpsert()`

```php
bundleUpsert($bundle_upsert_request): \ShadowSoftware\DabDash\Model\BundleUpsert200Response
```

Create or update a bundle deal (mix & match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least `quantity` units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type = \"percent\"      → discount_value is a percentage 0–100 (e.g. 20 = 20% off each unit).   discount_type = \"fixed\"        → discount_value is DOLLARS off PER UNIT (e.g. 5 = $5 off each).   discount_type = \"fixed_total\"  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity=4, discount_value=77 → \"any 4 for $77\").   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \"replace\" (default) sets membership to exactly variation_ids; \"add\" attaches them to the   existing set; \"detach\" removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant's timezone and stored as UTC. Pass null/omit   for an always-on bundle.  RULES (smart collections — GitHub #395):   Pass `rules` to turn this bundle into a rule-based (\"smart collection\") bundle: membership is   computed automatically from a list of AND-ed conditions instead of a fixed variation_ids list.   Each condition is {field, operator, value}:     field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status            (draft|active|archived) | tracking_type (weight|unit|matrix)     operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max])   On save, membership is recomputed immediately: variations matching every condition are added as   \"rule\" members. Pinned members and explicit exclusions (see bundle_membership) are never touched   or overridden by a rule — excluded beats pinned beats rule. The response reports how many   variations were added/removed by this recompute.   ALWAYS call bundle_rule_preview with your intended rules BEFORE saving them here — it shows   exactly what would match without writing anything, so you don't fire a rule blind against a   live tenant's catalog.   Passing `rules: []` or `rules: null` explicitly clears the bundle back to fully manual: existing   membership is left as-is (nothing is auto-removed), but it stops auto-updating and variation_ids   takes over again.   Omit `rules` entirely to leave a bundle's current rule state (manual or automatic) untouched.  Always call bundle_list first to get the bundle_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_upsert_request = new \ShadowSoftware\DabDash\Model\BundleUpsertRequest(); // \ShadowSoftware\DabDash\Model\BundleUpsertRequest

try {
    $result = $apiInstance->bundleUpsert($bundle_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->bundleUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_upsert_request** | [**\ShadowSoftware\DabDash\Model\BundleUpsertRequest**](../Model/BundleUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleUpsert200Response**](../Model/BundleUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignApplyTemplate()`

```php
campaignApplyTemplate($campaign_apply_template_request): \ShadowSoftware\DabDash\Model\CampaignApplyTemplate200Response
```

Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \"Choose template\" action in the vendor admin.  The template is rendered with the tenant's own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign's html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \"Your headline here\", etc.) — after applying, call campaign_get, then campaign_upsert to swap placeholder copy/prices inside the existing html_body (do NOT replace the whole body — that deletes the template layout). Use campaign_set_image for product photos. Or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_get + campaign_upsert (edit copy in place) + campaign_set_image (photos) → campaign_send_test (preview).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_apply_template_request = new \ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest(); // \ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest

try {
    $result = $apiInstance->campaignApplyTemplate($campaign_apply_template_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignApplyTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_apply_template_request** | [**\ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest**](../Model/CampaignApplyTemplateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignApplyTemplate200Response**](../Model/CampaignApplyTemplate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignControl()`

```php
campaignControl($campaign_control_request): \ShadowSoftware\DabDash\Model\CampaignControl200Response
```

Pause or resume a vendor email/SMS campaign.  pause: sets status=paused with paused_reason=manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_control_request = new \ShadowSoftware\DabDash\Model\CampaignControlRequest(); // \ShadowSoftware\DabDash\Model\CampaignControlRequest

try {
    $result = $apiInstance->campaignControl($campaign_control_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignControl: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_control_request** | [**\ShadowSoftware\DabDash\Model\CampaignControlRequest**](../Model/CampaignControlRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignControl200Response**](../Model/CampaignControl200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignFlyerStylePack()`

```php
campaignFlyerStylePack($campaign_flyer_style_pack_request): \ShadowSoftware\DabDash\Model\CampaignFlyerStylePack200Response
```

Teach campaign_generate_flyer what this shop's flyers look like, using their OWN past work.  A style pack is two things, both stored on the shop itself:   - Images: one or more past flyers or mood boards, held in the shop's media library.   - Art direction: short lines describing the look to carry forward (\"seasonal scenes, deal-dense     grids, deep green and gold, calm lower third\").  When a pack is saved, every flyer generation attaches it ahead of the product photos and asks the model to match the MOOD and COMPOSITION — never to copy text, logos or prices out of it.  ACTIONS:   - show  — what is saved right now: the direction lines and the images on file.   - set   — save direction lines and/or point the pack at media library images.             prompt_lines replaces the saved direction. media_ids MOVES those library assets into             the style-reference folder (they stay ordinary library assets, reusable elsewhere).   - clear — remove the direction lines, and with clear_images=true also take the images back out             of the style-reference folder. Nothing is deleted from the library.  HOW TO GET AN IMAGE IN: upload the flyer through the shop's media library (or send it to Dabby in chat, which files it there), then pass its media_id here. The pack lives entirely in the database, so it survives every deploy and can be changed at any time without one.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_flyer_style_pack_request = new \ShadowSoftware\DabDash\Model\CampaignFlyerStylePackRequest(); // \ShadowSoftware\DabDash\Model\CampaignFlyerStylePackRequest

try {
    $result = $apiInstance->campaignFlyerStylePack($campaign_flyer_style_pack_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignFlyerStylePack: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_flyer_style_pack_request** | [**\ShadowSoftware\DabDash\Model\CampaignFlyerStylePackRequest**](../Model/CampaignFlyerStylePackRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignFlyerStylePack200Response**](../Model/CampaignFlyerStylePack200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignGenerateFlyer()`

```php
campaignGenerateFlyer($campaign_generate_flyer_request): \ShadowSoftware\DabDash\Model\CampaignGenerateFlyer200Response
```

Generate BESPOKE flyer artwork for a campaign from the shop's own product photos, then drop it straight into the campaign design. This is the intended way to make a marketing email look like a designed flyer instead of a text layout — reach for it before writing copy, because the rest of the email is built to sit under the artwork.  WHAT IT PRODUCES:   A layered, dimensional flyer illustration in the shop's brand colours, composed around the real   packaging from the product photos you name. It renders NO text — the email supplies every word   as real text so it stays readable, translatable, and safe with images disabled.  REQUIREMENTS:   The shop must have its own AI key connected (Settings → Dab AI). Generation is billed to that   key, not to the platform, so a shop without one gets a clear message instead of a flyer. Use   the highest-quality model available on the key; that is the default and it is the point.   A shop that has saved a style pack (campaign_flyer_style_pack) automatically gets its own past   flyers attached as references, so the artwork matches the creative it already sends.  HOW TO USE IT:   - theme        — what the flyer is for, in plain words (\"September restock, cosy autumn mood\").   - product_ids  — the products to feature. Omit and it picks the shop's newest featured products                    that actually have photos.   - campaign_id  — a DRAFT campaign to place the artwork into. Omit to just add it to the media                    library and place it later with campaign_set_image.   - slot_index   — which image slot to fill (1-based). Defaults to the first, which is the hero.   - reference_media_ids — if the vendor just attached a past flyer (or any image) to THIS chat and                    asked for something \"in the same style\" or \"like this one\", pass its media_id                    here. The conversation shows it as \"[Library image saved: media_id=N, ...]\"                    right after the attachment. This is a one-off style/composition reference for                    this generation only — it steers mood, layout and palette, it is never                    reproduced verbatim, and it is not saved for next time. Never ask the vendor                    for a URL or to re-upload when their image is already attached to the chat —                    read its media_id from the conversation and pass it here instead. For a shop                    that wants the same look on every flyer going forward, save it once with                    campaign_flyer_style_pack instead of repeating reference_media_ids.  Typical flow: campaign_upsert (create) → campaign_apply_template (design) → campaign_upsert (real copy) → campaign_generate_flyer (hero artwork LAST) → campaign_send_test. Generate the artwork only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after the artwork is placed erases it.  The result always lands in the media library, so it can be reused on the storefront or in a later send without generating twice.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_generate_flyer_request = new \ShadowSoftware\DabDash\Model\CampaignGenerateFlyerRequest(); // \ShadowSoftware\DabDash\Model\CampaignGenerateFlyerRequest

try {
    $result = $apiInstance->campaignGenerateFlyer($campaign_generate_flyer_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignGenerateFlyer: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_generate_flyer_request** | [**\ShadowSoftware\DabDash\Model\CampaignGenerateFlyerRequest**](../Model/CampaignGenerateFlyerRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignGenerateFlyer200Response**](../Model/CampaignGenerateFlyer200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignLaunch()`

```php
campaignLaunch($campaign_launch_request): \ShadowSoftware\DabDash\Model\CampaignLaunch200Response
```

Start, schedule, or cancel a vendor campaign after it is ready.  ACTIONS:   send_now — draft → materialize + send (or resume paused). Requires spam/inbox gate.   schedule — draft → scheduled_at (tenant timezone local datetime string).   cancel — sending/paused/recovering/scheduled/failed → cancelled.  dry_run defaults TRUE. Applying requires dry_run=false AND confirm=true. Always campaign_spam_score / campaign_send_test first, then confirm with the vendor.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_launch_request = new \ShadowSoftware\DabDash\Model\CampaignLaunchRequest(); // \ShadowSoftware\DabDash\Model\CampaignLaunchRequest

try {
    $result = $apiInstance->campaignLaunch($campaign_launch_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignLaunch: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_launch_request** | [**\ShadowSoftware\DabDash\Model\CampaignLaunchRequest**](../Model/CampaignLaunchRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignLaunch200Response**](../Model/CampaignLaunch200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignRecipientsRequeue()`

```php
campaignRecipientsRequeue($campaign_recipients_requeue_request): \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeue200Response
```

Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \"sending\" rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors=true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run=false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_recipients_requeue_request = new \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest(); // \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest

try {
    $result = $apiInstance->campaignRecipientsRequeue($campaign_recipients_requeue_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignRecipientsRequeue: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_recipients_requeue_request** | [**\ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest**](../Model/CampaignRecipientsRequeueRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignRecipientsRequeue200Response**](../Model/CampaignRecipientsRequeue200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignSendTest()`

```php
campaignSendTest($campaign_send_test_request): \ShadowSoftware\DabDash\Model\CampaignSendTest200Response
```

Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email: routes by campaign mode —   smtp → tenant SMTP (requires complete SMTP settings)   mailgun → Mailgun Messages API via a Ready sending name on the campaign   webhook → posts a test payload to the campaign webhook (no inbox email) Pass to_email for smtp/mailgun. Webhook mode uses to_email only as the sample recipient identity in the payload.  Text (SMS): Twilio. Pass to_phone. Requires Twilio connected.  Preview only — does NOT start the campaign, touch the audience, or record stats.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_send_test_request = new \ShadowSoftware\DabDash\Model\CampaignSendTestRequest(); // \ShadowSoftware\DabDash\Model\CampaignSendTestRequest

try {
    $result = $apiInstance->campaignSendTest($campaign_send_test_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignSendTest: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_send_test_request** | [**\ShadowSoftware\DabDash\Model\CampaignSendTestRequest**](../Model/CampaignSendTestRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignSendTest200Response**](../Model/CampaignSendTest200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignSetImage()`

```php
campaignSetImage($campaign_set_image_request): \ShadowSoftware\DabDash\Model\CampaignSetImage200Response
```

Drop an image from the tenant's media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant's own library) and it fills an image placeholder in the campaign body with that image, using the library image's own public URL and alt text. The URL is on the tenant's public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \"Add image\". By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_upsert (real copy) → campaign_set_image (fill each placeholder LAST) → campaign_send_test. Fill placeholders only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after an image is placed erases it.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_set_image_request = new \ShadowSoftware\DabDash\Model\CampaignSetImageRequest(); // \ShadowSoftware\DabDash\Model\CampaignSetImageRequest

try {
    $result = $apiInstance->campaignSetImage($campaign_set_image_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignSetImage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_set_image_request** | [**\ShadowSoftware\DabDash\Model\CampaignSetImageRequest**](../Model/CampaignSetImageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignSetImage200Response**](../Model/CampaignSetImage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignUpsert()`

```php
campaignUpsert($campaign_upsert_request): \ShadowSoftware\DabDash\Model\CampaignUpsert200Response
```

Create or edit a vendor email/SMS campaign (vendor → their customers; NOT platform drip-to-leads). Completed campaigns cannot be edited.  CREATE (no campaign_id): draft. Email needs name+subject (html_body optional — then campaign_apply_template). SMS needs name+sms_body and connected Twilio.  UPDATE (campaign_id): only passed fields change. Drafts accept all fields. Sending/paused accept content only (email: name/subject/html_body/plain_body/fallback_first_name; SMS: name/sms_body/fallback_first_name).  TRANSPORT (email): mode = smtp | webhook | mailgun.   mailgun: require Ready mailgun_domain_ids (from tenant_mailgun_manage show); optional   mailgun_pace drip|blast.   webhook: prefer saved_webhook_id, or inline webhook_url (+ optional headers/health). AUDIENCE: contact_list_ids (manual and/or Smart Lists) and/or audience_includes_customers (default true). Lead lists cannot use smtp — use mailgun or webhook. SMS may attach Smart Lists only (not imported manual lists). Discover lists with contact_list_manage / smart_list_manage.  Optional: throttle_per_minute/hour, send_interval_minutes (1–30), send_window_*.  Does not start a live send — use campaign_send_test, then campaign_launch (confirm) or the vendor admin Send button.  ORDERING: html_body is a full replace. A table-based / already-designed body is kept exactly as sent — product and category photos from the previous version are NOT re-prepended as hero banners. Bare/fragment copy updates still carry previously appended flyer artwork (campaign_generate_flyer / campaign_set_image) so a rewrite does not wipe the art. Pass html_body \"\" to clear the body on purpose.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_upsert_request = new \ShadowSoftware\DabDash\Model\CampaignUpsertRequest(); // \ShadowSoftware\DabDash\Model\CampaignUpsertRequest

try {
    $result = $apiInstance->campaignUpsert($campaign_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_upsert_request** | [**\ShadowSoftware\DabDash\Model\CampaignUpsertRequest**](../Model/CampaignUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignUpsert200Response**](../Model/CampaignUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `catalogCollapse()`

```php
catalogCollapse($catalog_collapse_request): \ShadowSoftware\DabDash\Model\CatalogCollapse200Response
```

Merges a group of size-split products into ONE product with size options.  Turns \"Blue Dream - 3.5G\", \"Blue Dream - 7G\", \"Blue Dream - 28G\" (three separate products, each with one \"Default\" option) into a single \"Blue Dream\" product that sells by weight with 3.5g / 7g / 28g options. Stock is added together into one shared pool, and each old price becomes the price of its size.  Run catalog_flattening_audit first to see the groups and their exact base_name.  SAFETY: - dry_run defaults to TRUE. Nothing changes until you pass dry_run=false. The dry run returns   the exact sizes, prices, stock, and which products would be removed. - Past orders are never affected — they keep the product name and price the customer saw. - Refused when two products in the group claim the same size, or when any product belongs to   another store. - This removes the duplicate products. It cannot be undone from here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$catalog_collapse_request = new \ShadowSoftware\DabDash\Model\CatalogCollapseRequest(); // \ShadowSoftware\DabDash\Model\CatalogCollapseRequest

try {
    $result = $apiInstance->catalogCollapse($catalog_collapse_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->catalogCollapse: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **catalog_collapse_request** | [**\ShadowSoftware\DabDash\Model\CatalogCollapseRequest**](../Model/CatalogCollapseRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CatalogCollapse200Response**](../Model/CatalogCollapse200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `catalogImport()`

```php
catalogImport($catalog_import_request): \ShadowSoftware\DabDash\Model\CatalogImport200Response
```

Bulk-create categories and products in one call, for onboarding a store from a scraped or exported catalog. Built on category_manage, product_manage, media_upload and product_image_assign, so results match creating each item by hand.  Pass categories (name, optional parent name) and/or products (name, price in dollars, optional sku, stock_quantity, description, category names, image_url, or a complete expected_variation_count plus named variations list). Categories are matched by name (existing ones are reused, never duplicated); products are matched by exact name (existing ones are skipped, never overwritten). Up to 100 products per call.  dry_run defaults true: the first call reports what would be created and skipped. Pass dry_run=false to write. Image failures never block a product; they are listed per item. Tiered pricing, bundles and strain-specific data are set afterwards with pricing_structure_*, bundle_* and product_manage.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$catalog_import_request = new \ShadowSoftware\DabDash\Model\CatalogImportRequest(); // \ShadowSoftware\DabDash\Model\CatalogImportRequest

try {
    $result = $apiInstance->catalogImport($catalog_import_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->catalogImport: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **catalog_import_request** | [**\ShadowSoftware\DabDash\Model\CatalogImportRequest**](../Model/CatalogImportRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CatalogImport200Response**](../Model/CatalogImport200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `catalogSaleManage()`

```php
catalogSaleManage($catalog_sale_manage_request): \ShadowSoftware\DabDash\Model\CatalogSaleManage200Response
```

Apply or remove a category shelf sale. The percent or dollar amount comes off the Was price (the compare-at price when the item is already on sale, otherwise the current price), never off the current sale price. Each changed variation's selling price and Was price are remembered. remove with mode \"restore\" writes both back. mode \"keep\" leaves the new price and drops the Was badge, forgetting the previous sale.  Selected categories include their subcategories. Products in other categories stay untouched. One category sale at a time, and it cannot run during a storewide sale. A storewide sale covers the whole catalog and discounts the current selling price instead.  This is not a coupon. coupon_upsert only discounts the current cart price at checkout and does not change or remember shelf prices.  ACTIONS:   show (default): whether a category sale is active and how many prices it remembers.   apply: type \"percent\" (0-100) or \"flat\" (dollars), amount, and category_ids.   remove: mode \"restore\" (default) or \"keep\".  apply and remove preview unless dry_run=false and confirm=true. Resolve category ids with category_manage before applying.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$catalog_sale_manage_request = new \ShadowSoftware\DabDash\Model\CatalogSaleManageRequest(); // \ShadowSoftware\DabDash\Model\CatalogSaleManageRequest

try {
    $result = $apiInstance->catalogSaleManage($catalog_sale_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->catalogSaleManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **catalog_sale_manage_request** | [**\ShadowSoftware\DabDash\Model\CatalogSaleManageRequest**](../Model/CatalogSaleManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CatalogSaleManage200Response**](../Model/CatalogSaleManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `catalogVariantCompact()`

```php
catalogVariantCompact($catalog_variant_compact_request): \ShadowSoftware\DabDash\Model\CatalogVariantCompact200Response
```

Safely merges explicit same-family unit products into one product with named variants. Preview first; live mode preserves the survivor, moves variations, unions categories, records old-slug redirects, and removes absorbed products atomically.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$catalog_variant_compact_request = new \ShadowSoftware\DabDash\Model\CatalogVariantCompactRequest(); // \ShadowSoftware\DabDash\Model\CatalogVariantCompactRequest

try {
    $result = $apiInstance->catalogVariantCompact($catalog_variant_compact_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->catalogVariantCompact: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **catalog_variant_compact_request** | [**\ShadowSoftware\DabDash\Model\CatalogVariantCompactRequest**](../Model/CatalogVariantCompactRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CatalogVariantCompact200Response**](../Model/CatalogVariantCompact200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `categoryManage()`

```php
categoryManage($category_manage_request): \ShadowSoftware\DabDash\Model\CategoryManage200Response
```

List, create, update, or delete a tenant's storefront categories.  ACTIONS:   list   (default): return every category with id, name, slug, parent, sort_order,          is_active, is_featured, and image_url. Always call this first to find a          category_id before update/delete, and to check for slug collisions before create.   create: requires name (slug is auto-generated from name if omitted).   update: requires category_id. Only the fields you pass are changed.   delete: requires category_id and confirm=true. Refuses if the category still has          products or children attached (detach or reassign them first).  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites branding onto). Omit both to leave images untouched.  Always call action=list first to confirm category_id / slug before update or delete.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$category_manage_request = new \ShadowSoftware\DabDash\Model\CategoryManageRequest(); // \ShadowSoftware\DabDash\Model\CategoryManageRequest

try {
    $result = $apiInstance->categoryManage($category_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->categoryManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **category_manage_request** | [**\ShadowSoftware\DabDash\Model\CategoryManageRequest**](../Model/CategoryManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CategoryManage200Response**](../Model/CategoryManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `contactCleanup()`

```php
contactCleanup($contact_cleanup_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Removes contacts that failed email validation from an imported list.  Typical use: a CSV import brought in thousands of addresses, the validator graded them, and the list is now full of dead addresses that will bounce and hurt sending reputation.  SAFETY: - dry_run defaults to TRUE. Nothing is deleted until you pass dry_run=false. The dry run   returns exactly how many contacts match and a sample of them. - Only statuses \"invalid\" and \"risky\" can be purged. \"unknown\" means the validator has not   graded that contact YET — those are never deleted, and asking for them is refused. - Scoped to one contact list when list_id is given, otherwise the whole store. - Contacts are deleted, not unsubscribed. This cannot be undone from here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$contact_cleanup_request = new \ShadowSoftware\DabDash\Model\ContactCleanupRequest(); // \ShadowSoftware\DabDash\Model\ContactCleanupRequest

try {
    $result = $apiInstance->contactCleanup($contact_cleanup_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->contactCleanup: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **contact_cleanup_request** | [**\ShadowSoftware\DabDash\Model\ContactCleanupRequest**](../Model/ContactCleanupRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `contactListManage()`

```php
contactListManage($contact_list_manage_request): \ShadowSoftware\DabDash\Model\ContactListManage200Response
```

Discover and manage contact lists (manual + Smart) for campaigns, coupons, and Klaviyo.  ACTIONS:   list (default): all lists with id, name, kind (manual|smart), default_type, contacts_count,     usable_for hints (email/sms/coupon/klaviyo).   show: one list by list_id (includes Smart List definition when kind=smart).   create_manual: create an empty imported/manual list (name required; default_type lead|customer).   rename: update name/description/default_type on a manual list (not Smart — use smart_list_manage).   delete: remove a list (dry_run default true; applying needs dry_run=false + confirm=true).  CSV import stays in the vendor admin. Use smart_list_manage for Smart List CRUD/preview.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$contact_list_manage_request = new \ShadowSoftware\DabDash\Model\ContactListManageRequest(); // \ShadowSoftware\DabDash\Model\ContactListManageRequest

try {
    $result = $apiInstance->contactListManage($contact_list_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->contactListManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **contact_list_manage_request** | [**\ShadowSoftware\DabDash\Model\ContactListManageRequest**](../Model/ContactListManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactListManage200Response**](../Model/ContactListManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `couponUpsert()`

```php
couponUpsert($coupon_upsert_request): \ShadowSoftware\DabDash\Model\CouponUpsert200Response
```

Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those). A coupon discounts the current cart price. It does not change the shelf price or the Was price, and it cannot put an earlier sale back. For a category shelf sale off the Was price that remembers and restores the previous sale, use catalog_sale_manage.  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type = \"percentage\"     → value is a percentage 0-100 off the order subtotal.   type = \"fixed\"          → value is DOLLARS off the order subtotal (e.g. 10 = $10 off).   type = \"free_delivery\"  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \"code-triggered freebie\": a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\"email\"|\"phone\"|\"both\") controls how max_uses_per_customer is enforced. Using \"phone\" or \"both\" REQUIRES the tenant's \"Require phone at checkout\" setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant's timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to=\"products\" or \"categories\" restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to=\"products\") or category ids (when   applies_to=\"categories\") — all ids must belong to this tenant. Passing applies_to=\"products\"/   \"categories\" with an empty or omitted applies_to_ids behaves the same as \"all\" (no scope   configured yet). Switching back to applies_to=\"all\" does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids=[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \"mounting ladder\" so its discount climbs with each successive order a   customer's delivery subscription generates, pegging at a ceiling — e.g. order 1 = 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  contact_list_id optionally restricts redemption to a tenant-owned manual or Smart List. Smart Lists are re-evaluated live; manual-list customers and leads are matched by contact email. The restriction is checked at cart apply and checkout redemption time. Pass null to remove it.  Always call coupon_list first to get the coupon_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$coupon_upsert_request = new \ShadowSoftware\DabDash\Model\CouponUpsertRequest(); // \ShadowSoftware\DabDash\Model\CouponUpsertRequest

try {
    $result = $apiInstance->couponUpsert($coupon_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->couponUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **coupon_upsert_request** | [**\ShadowSoftware\DabDash\Model\CouponUpsertRequest**](../Model/CouponUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CouponUpsert200Response**](../Model/CouponUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerBlockManage()`

```php
customerBlockManage($customer_block_manage_request): \ShadowSoftware\DabDash\Model\CustomerBlockManage200Response
```

Block or unblock a customer's current email and/or phone using existing tenant Security lists. Preview by default. Apply only on explicit user instruction with dry_run=false, confirm=true, a reason and expected_snapshot from status/preview. Unblocking removes the selected shared identifier blocks, including pre-existing ones; affects anyone using them. Other restrictions remain. Does not delete customers, change orders or create an account-ID ban.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_block_manage_request = new \ShadowSoftware\DabDash\Model\CustomerBlockManageRequest(); // \ShadowSoftware\DabDash\Model\CustomerBlockManageRequest

try {
    $result = $apiInstance->customerBlockManage($customer_block_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->customerBlockManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_block_manage_request** | [**\ShadowSoftware\DabDash\Model\CustomerBlockManageRequest**](../Model/CustomerBlockManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerBlockManage200Response**](../Model/CustomerBlockManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerMessageDraftsCreate()`

```php
customerMessageDraftsCreate($customer_message_drafts_create_request): \ShadowSoftware\DabDash\Model\CustomerMessageDraftsCreate200Response
```

Create reply drafts only when the user explicitly asks in their current message to draft or prepare customer replies. Never call merely because unanswered messages exist. This tool never sends messages; Dabby presents each original customer message and proposed reply in chat for explicit confirmation.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_message_drafts_create_request = new \ShadowSoftware\DabDash\Model\CustomerMessageDraftsCreateRequest(); // \ShadowSoftware\DabDash\Model\CustomerMessageDraftsCreateRequest

try {
    $result = $apiInstance->customerMessageDraftsCreate($customer_message_drafts_create_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->customerMessageDraftsCreate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_message_drafts_create_request** | [**\ShadowSoftware\DabDash\Model\CustomerMessageDraftsCreateRequest**](../Model/CustomerMessageDraftsCreateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerMessageDraftsCreate200Response**](../Model/CustomerMessageDraftsCreate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerUpdate()`

```php
customerUpdate($customer_update_request): \ShadowSoftware\DabDash\Model\CustomerUpdate200Response
```

Update a customer's contact fields (name, email, phone) and/or suppress marketing consent (email_opt_out, sms_marketing_opt_out, sms_notifications_muted — one-way, cannot un-suppress). Verification, loyalty, and other DabDash-owned fields cannot be set here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_update_request = new \ShadowSoftware\DabDash\Model\CustomerUpdateRequest(); // \ShadowSoftware\DabDash\Model\CustomerUpdateRequest

try {
    $result = $apiInstance->customerUpdate($customer_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->customerUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_update_request** | [**\ShadowSoftware\DabDash\Model\CustomerUpdateRequest**](../Model/CustomerUpdateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerUpdate200Response**](../Model/CustomerUpdate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `deliveryDelayMessageDraftsCreate()`

```php
deliveryDelayMessageDraftsCreate($delivery_delay_message_drafts_create_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Create delivery-delay email drafts only when the user explicitly asks in their current message to prepare or send a delivery update. Use only after order status confirms the customer is out for delivery. This tool never sends email; Dabby presents each proposal in chat for explicit confirmation.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$delivery_delay_message_drafts_create_request = new \ShadowSoftware\DabDash\Model\DeliveryDelayMessageDraftsCreateRequest(); // \ShadowSoftware\DabDash\Model\DeliveryDelayMessageDraftsCreateRequest

try {
    $result = $apiInstance->deliveryDelayMessageDraftsCreate($delivery_delay_message_drafts_create_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->deliveryDelayMessageDraftsCreate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **delivery_delay_message_drafts_create_request** | [**\ShadowSoftware\DabDash\Model\DeliveryDelayMessageDraftsCreateRequest**](../Model/DeliveryDelayMessageDraftsCreateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `deliverySettingsManage()`

```php
deliverySettingsManage($delivery_settings_manage_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Show or configure tenant delivery operations: logistics gate, driver self-serve, customer tracking visibility, proof of delivery, driver notification suppressions, and auto-dispatch. Set uses dry_run=true by default and writes are audited. Enable delivery logistics before enabling dependent driver controls. WhatsApp is not changed by this tool.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$delivery_settings_manage_request = new \ShadowSoftware\DabDash\Model\DeliverySettingsManageRequest(); // \ShadowSoftware\DabDash\Model\DeliverySettingsManageRequest

try {
    $result = $apiInstance->deliverySettingsManage($delivery_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->deliverySettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **delivery_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\DeliverySettingsManageRequest**](../Model/DeliverySettingsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `freebieUpsert()`

```php
freebieUpsert($freebie_upsert_request): \ShadowSoftware\DabDash\Model\FreebieUpsert200Response
```

Create or update a freebie rule (\"spend $X, get a free item\") on behalf of a tenant. Freebies are evaluated on every cart change: a rule fires once its spend_threshold is met, adding `quantity` of the configured product/variation to the cart (distinct from bundles, which fire on cart CONTENTS/quantity — use bundle_upsert for those).  UPDATE MODE (freebie_id provided):   Edits the freebie. Only the fields you pass are changed; omitted fields are left as-is,   EXCEPT category_ids, which — like bundle_upsert's variation_ids — fully replaces the category   set whenever passed (pass an empty array to clear all categories).  CREATE MODE (no freebie_id):   Creates a new freebie. name, product_id, spend_threshold, and quantity are required.  product_id and variation_id (if given) MUST belong to the same tenant — foreign ids are rejected, not silently ignored (unlike bundle_upsert's variation_ids, since a freebie needs exactly one product to give away, not a set).  spend_threshold is entered in DOLLARS (e.g. 50 for a $50 minimum spend) — the tool stores cents internally.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order.   false  → this rule cannot combine with other freebie rules.   Does NOT multiply this rule's own quantity by how many multiples of spend_threshold the cart   reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant's timezone and stored as UTC. Pass   null/omit for an always-on freebie.  Always call freebie_list first to get the freebie_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$freebie_upsert_request = new \ShadowSoftware\DabDash\Model\FreebieUpsertRequest(); // \ShadowSoftware\DabDash\Model\FreebieUpsertRequest

try {
    $result = $apiInstance->freebieUpsert($freebie_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->freebieUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **freebie_upsert_request** | [**\ShadowSoftware\DabDash\Model\FreebieUpsertRequest**](../Model/FreebieUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\FreebieUpsert200Response**](../Model/FreebieUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `loyaltyManage()`

```php
loyaltyManage($loyalty_manage_request): \ShadowSoftware\DabDash\Model\LoyaltyManage200Response
```

Show or configure a store's points-based loyalty program, including vendor-defined lifetime-point tiers with automatic percentage discounts.  ACTIONS:   show (default): current settings.   set: pass only what changes. dry_run defaults true; pass dry_run=false to save.   set_customer_lifetime: set one explicitly sourced customer's lifetime total. This is     for a one-time migration and is dry-run by default; it never changes spendable balance.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$loyalty_manage_request = new \ShadowSoftware\DabDash\Model\LoyaltyManageRequest(); // \ShadowSoftware\DabDash\Model\LoyaltyManageRequest

try {
    $result = $apiInstance->loyaltyManage($loyalty_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->loyaltyManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **loyalty_manage_request** | [**\ShadowSoftware\DabDash\Model\LoyaltyManageRequest**](../Model/LoyaltyManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\LoyaltyManage200Response**](../Model/LoyaltyManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaCompose()`

```php
mediaCompose($media_compose_request): \ShadowSoftware\DabDash\Model\MediaCompose200Response
```

Build a finished campaign/storefront creative ON THE SERVER: take a base picture, drop the vendor's logo on top, add a headline and subtitle, and save the RESULT straight into the tenant's media library. Returns a media_id + public URL ready to place with campaign_set_image, category_manage, widget_manage, tenant_branding_manage, product_image_assign, or tenant_blog_upsert.  PROVIDE THE BASE (exactly one):   - base_media_id — preferred for tenant-owned photos already in the library (media_list /     media_upload). Keeps the source visible in Admin → Media.   - base_url      — public https URL for external/generated artwork. Only the composed     OUTPUT is saved as a library row; the raw URL bytes are not separately ingested. Prefer     media_upload → base_media_id when the photo should remain in the media library (#184).  PROVIDE THE LOGO (optional, at most one):   - logo_media_id / logo_url — same two options as the base (prefer logo_media_id for the     tenant logo so it stays linked in Media).   THE LOGO IS NEVER ALTERED. It is scaled to fit (aspect ratio locked) and placed as-is —   its colours, strokes and transparency are left exactly as supplied. For legibility on busy   artwork use the `scrim` option, which darkens the area BEHIND the logo and text.  LAYOUT: logo on top, headline under it, subtitle under that — the block is centred horizontally and positioned with logo_position (top / center / bottom). Sizes are given as a fraction of the base image width so they scale with any canvas. Long text is word-wrapped automatically; the response's `wrapped` flag tells you when that happened so you can shorten it.  CONSTRAINTS: sources max 5 MB, JPEG/PNG/WebP/GIF, max 8000x8000. The finished image goes through the same pipeline as media_upload — re-encoded to WebP, resized to fit, de-duplicated by content — so identical inputs return the existing media_id instead of a duplicate.  Typical flow: media_upload (or media_list) → media_compose → place with media_id.  STOREFRONT SWAG MODE (category_id or widget_id): instead of the manual layout above, pass category_id or widget_id to run the platform's own storefront branding pipeline (SwagImagesService) against that exact category or widget — the same compositor used by the dev seeder and vendor admin: tenant logo eyebrow, Bebas Neue headline (shrunk to fit long names automatically), brand-colour accent rule, and a sub-label (the tenant's live domain for categories, the widget's own subtitle for widgets). Pass base_media_id or base_url to set/replace the row's base_image_path first; omit both to recompose from whatever base_image_path is already saved. Saves directly to image_path on that category or widget — this is an internal/admin tool, not exposed to vendors.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_compose_request = new \ShadowSoftware\DabDash\Model\MediaComposeRequest(); // \ShadowSoftware\DabDash\Model\MediaComposeRequest

try {
    $result = $apiInstance->mediaCompose($media_compose_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->mediaCompose: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_compose_request** | [**\ShadowSoftware\DabDash\Model\MediaComposeRequest**](../Model/MediaComposeRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\MediaCompose200Response**](../Model/MediaCompose200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaList()`

```php
mediaList($media_list_request): \ShadowSoftware\DabDash\Model\MediaList200Response
```

List the images in a tenant's media library — id, public URL, dimensions, filename, folder, and alt text. Use this to find the media_id of an image to place into a campaign with campaign_set_image, instead of guessing. Returns newest first.  Filter with `folder` (exact match) or `search` (filename substring). `visibility` defaults to \"public\" (the emailable assets); pass \"all\" or \"private\" to widen. Confirm the tenant_slug with tenant_list first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_list_request = new \ShadowSoftware\DabDash\Model\MediaListRequest(); // \ShadowSoftware\DabDash\Model\MediaListRequest

try {
    $result = $apiInstance->mediaList($media_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->mediaList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_list_request** | [**\ShadowSoftware\DabDash\Model\MediaListRequest**](../Model/MediaListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\MediaList200Response**](../Model/MediaList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaUpload()`

```php
mediaUpload($media_upload_request): \ShadowSoftware\DabDash\Model\MediaUpload200Response
```

Upload an image into a tenant's media library (the same library the vendor admin uses). The image is ingested through the platform's shared media pipeline — re-encoded to WebP, resized to fit, EXIF- oriented, content-addressed for dedup — and a media_assets row is created. It ALWAYS lands in the library, and the tool returns the new media_id and public URL.  PLACE the media_id with the matching tool (never hotlink the URL as a product/blog/hero path):   - campaign_set_image — email campaign HTML   - category_manage / widget_manage — storefront images (media_id / base_media_id)   - tenant_branding_manage — store logo (media_id) or homepage hero (hero_media_id)   - product_image_assign — product featured / gallery   - tenant_blog_upsert — vendor blog featured image   - media_compose — base_media_id / logo_media_id for composed creatives  PROVIDE THE IMAGE ONE OF THREE WAYS (exactly one):   - source_url    — fetch the image from a public http(s) URL (preferred for remote agents).   - source_path   — read a local file path on the server/agent host (often broken on prod FS).   - source_base64 — raw base64 of the image bytes (no data: prefix needed; a data: prefix is stripped).  CONSTRAINTS: max 5 MB; JPEG, PNG, WebP, or GIF. Identical bytes already in the library are de-duped (you get the existing media_id back). Uploads default to public visibility so the image is emailable.  Optional alt_text and folder help organise and caption the asset. Confirm the tenant_slug with tenant_list first. To see what's already in the library, use media_list.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_upload_request = new \ShadowSoftware\DabDash\Model\MediaUploadRequest(); // \ShadowSoftware\DabDash\Model\MediaUploadRequest

try {
    $result = $apiInstance->mediaUpload($media_upload_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->mediaUpload: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_upload_request** | [**\ShadowSoftware\DabDash\Model\MediaUploadRequest**](../Model/MediaUploadRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\MediaUpload200Response**](../Model/MediaUpload200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `notificationSettingsManage()`

```php
notificationSettingsManage($notification_settings_manage_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Show or configure tenant-wide vendor order, low-stock, digest, restock, and storefront enquiry notification preferences. This tool deliberately excludes per-user opt-outs, SMS provider configuration, and WhatsApp. Set uses dry_run=true by default and writes are audited.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$notification_settings_manage_request = new \ShadowSoftware\DabDash\Model\NotificationSettingsManageRequest(); // \ShadowSoftware\DabDash\Model\NotificationSettingsManageRequest

try {
    $result = $apiInstance->notificationSettingsManage($notification_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->notificationSettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **notification_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\NotificationSettingsManageRequest**](../Model/NotificationSettingsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `orderUpsert()`

```php
orderUpsert($order_upsert_request): \ShadowSoftware\DabDash\Model\OrderUpsert200Response
```

Upsert WooCommerce orders into this tenant (idempotent WP-{id} order numbers). Used by dabdash-for-woocommerce continuous order sync and small residual imports. Pass at most 100 rows matching the wordpress_commerce_import orders shape. Preview defaults on; applying requires dry_run=false and confirm=true.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$order_upsert_request = new \ShadowSoftware\DabDash\Model\OrderUpsertRequest(); // \ShadowSoftware\DabDash\Model\OrderUpsertRequest

try {
    $result = $apiInstance->orderUpsert($order_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->orderUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **order_upsert_request** | [**\ShadowSoftware\DabDash\Model\OrderUpsertRequest**](../Model/OrderUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\OrderUpsert200Response**](../Model/OrderUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureAssign()`

```php
pricingStructureAssign($pricing_structure_assign_request): \ShadowSoftware\DabDash\Model\PricingStructureAssign200Response
```

Assign a shared bundle pricing structure to one or more products. Re-syncs variations for each reassigned product. Automatically deletes orphaned inline (hidden) structures when replacing them.  SAFETY RULES enforced by this tool: - The target structure_id must be a BUNDLE (is_hidden=false). Inline structures cannot be assigned   to products this way — that would violate the 1:1 contract. - If a product's current structure is inline (hidden) and this product is its only consumer   (product_count == 1), the old inline structure is deleted automatically. - If a product's current structure is inline but product_count > 1, assignment is refused for that   product with an explanation — this is a data anomaly that needs manual resolution. - Each product result includes a status: assigned | skipped (already on this structure) | refused.  Use pricing_structure_list to get valid structure IDs before calling this tool.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_assign_request = new \ShadowSoftware\DabDash\Model\PricingStructureAssignRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureAssignRequest

try {
    $result = $apiInstance->pricingStructureAssign($pricing_structure_assign_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureAssign: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_assign_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureAssignRequest**](../Model/PricingStructureAssignRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureAssign200Response**](../Model/PricingStructureAssign200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureDelete()`

```php
pricingStructureDelete($pricing_structure_delete_request): \ShadowSoftware\DabDash\Model\PricingStructureDelete200Response
```

Delete one or more pricing structures by ID.  SAFETY RULES enforced by this tool: - BUNDLE structures (is_hidden=false): always deletable unless products are still assigned.   Pass force=true to delete even when products are assigned (use only after migrating them). - INLINE structures (is_hidden=true): only deletable when product_count=0 (orphaned).   Inline structures with products attached cannot be deleted — use pricing_structure_assign   to move the product to a bundle first, which auto-cleans the inline structure.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_delete_request = new \ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest

try {
    $result = $apiInstance->pricingStructureDelete($pricing_structure_delete_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_delete_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest**](../Model/PricingStructureDeleteRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureDelete200Response**](../Model/PricingStructureDelete200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureList()`

```php
pricingStructureList($pricing_structure_list_request): \ShadowSoftware\DabDash\Model\PricingStructureList200Response
```

List all pricing structures for a tenant with their kind (inline|bundle), product count, tracking type, and tier summary. Always call this before pricing_structure_upsert or pricing_structure_assign to get structure IDs and confirm which structures are bundles vs inline (1:1 product) structures.  kind=inline  → hidden 1:1 structure tied to exactly one product (is_hidden=true) kind=bundle  → shared structure visible on /admin/pricing, used by 0 or more products (is_hidden=false)

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_list_request = new \ShadowSoftware\DabDash\Model\PricingStructureListRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureListRequest

try {
    $result = $apiInstance->pricingStructureList($pricing_structure_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_list_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureListRequest**](../Model/PricingStructureListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureList200Response**](../Model/PricingStructureList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureRestore()`

```php
pricingStructureRestore($pricing_structure_restore_request): \ShadowSoftware\DabDash\Model\PricingStructureRestore200Response
```

Surgical restore tool. Rebuilds a single product's pricing structure and variations EXACTLY to a specified state. Bypasses the standard syncVariationsForProduct routine — you control every field.  Use this AFTER inventory_audit_lookup has revealed the pre-incident state of variations whose names/prices/stock were destroyed by an erroneous bundle reassignment.  Behaviour: - Creates a NEW inline (hidden, 1:1) pricing structure with the given tracking_type, tier   definitions, and a name like \"Product: <product_name> (Hidden)\". Old structure linkage is   replaced. The previous structure is NOT deleted by this tool. - For each tier in the spec, finds-or-creates a variation. Matching is by `restore_variation_id`   if provided, else by name. If found, the variation is updated in place (preserving its id and   its audit-log history). If not found, a new variation is created. - Stock_quantity is set EXACTLY to the value specified — this is the whole point of the tool. - Sets product.tracking_type, product.inventory_mode, product.base_unit per the new structure. - Variations on the product not referenced by any tier in the spec are DEACTIVATED (is_active=false)   so they stop being shown but their audit history is retained. Pass `delete_unreferenced=true` to   hard-delete them instead.  SAFETY: - Wrap each call in its own transaction. - Will refuse if the new tracking_type is incompatible with stored cost data. - Inline-only by design — bundles are not recreated by this tool. Use pricing_structure_assign   to put the product on a bundle if that's what you want.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_restore_request = new \ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest

try {
    $result = $apiInstance->pricingStructureRestore($pricing_structure_restore_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureRestore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_restore_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest**](../Model/PricingStructureRestoreRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureRestore200Response**](../Model/PricingStructureRestore200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureUpsert()`

```php
pricingStructureUpsert($pricing_structure_upsert_request): \ShadowSoftware\DabDash\Model\PricingStructureUpsert200Response
```

Create or edit a pricing structure's tiers, name, and tracking type. Operates in three modes:  BUNDLE MODE (structure_id provided, structure is not hidden):   Edit a shared bundle structure visible on /admin/pricing. Tiers are replaced and ALL products   linked to the bundle are re-synced. Returns how many products were affected as a warning.  INLINE MODE (product_slug or product_id provided, no structure_id):   Edit the hidden 1:1 pricing structure for a single product. Tiers are replaced and variations   are re-synced for that product only. Refuses if the product currently uses a bundle structure —   use pricing_structure_assign to detach from the bundle first.  CREATE BUNDLE MODE (no structure_id, no product_slug/product_id):   Create a new shared bundle structure. Does not link it to any products.  SAFETY RULES enforced by this tool: - Never accepts structure_id pointing to a hidden (inline) structure — always go via product_slug/product_id. - Tiers for weight/matrix types must have weight_grams > 0. - Tiers for simple type: only the first tier is used; name and weight_grams are normalised. - Prices are accepted as dollar amounts (e.g. 12.99) and converted to cents internally.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_upsert_request = new \ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest

try {
    $result = $apiInstance->pricingStructureUpsert($pricing_structure_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_upsert_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest**](../Model/PricingStructureUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureUpsert200Response**](../Model/PricingStructureUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productFeatureManage()`

```php
productFeatureManage($product_feature_manage_request): \ShadowSoftware\DabDash\Model\ProductFeatureManage200Response
```

List or set which products appear in a tenant's curated storefront home grids (Classic and Modern). Each section shows up to 8 products, ordered by the tenant's default product sort, and hides itself when empty.  SECTIONS (pass as `section`):   featured (default) → is_featured → \"Featured Products\"   deals              → is_featured_deal → \"Featured Deals\"   new                → is_featured_new → \"Featured New Inventory\"  Optional schedule windows (starts_at / ends_at) are tenant-local calendar dates stored as UTC. Null start = live now; null end = never expires. Storefront membership is query-time (flag on + within window).  This does not change the Modern hero's optional spotlight card; use storefront_theme_manage or store_settings_manage (hero_spotlight_mode / hero_spotlight_product) for that.  ACTIONS:   list (default): return every product flagged for the chosen section          (including scheduled/ended) with id, slug, name, stock_status,          the three featured flags, schedule window, and status          (live|scheduled|ended).   set: pass product_ids (array) and featured (bool) to set the section's          flag on those products. Optional starts_at / ends_at apply when          featuring; turning featured off clears that section's window.          Unlisted products are left untouched.  Products must be resolved to ids first (product_inspect or this tool's list action). Featuring an out-of-stock product is allowed but usually undesirable — check stock_status in the list output before featuring.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_feature_manage_request = new \ShadowSoftware\DabDash\Model\ProductFeatureManageRequest(); // \ShadowSoftware\DabDash\Model\ProductFeatureManageRequest

try {
    $result = $apiInstance->productFeatureManage($product_feature_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productFeatureManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_feature_manage_request** | [**\ShadowSoftware\DabDash\Model\ProductFeatureManageRequest**](../Model/ProductFeatureManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductFeatureManage200Response**](../Model/ProductFeatureManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productImageAssign()`

```php
productImageAssign($product_image_assign_request): \ShadowSoftware\DabDash\Model\ProductImageAssign200Response
```

Set a product's featured image and/or gallery from the tenant media library. Pass media_id (from media_list / media_upload) for featured_image, and/or gallery_media_ids to replace gallery_images. Does not upload bytes and does not create media_assets rows — ingest first with media_upload. Assets must be public (private library items have no storefront URL).  dry_run (default true) previews without writing. overwrite (default false) refuses to replace an existing featured_image unless true. clear_featured clears featured_image without setting a new one.  Distinct from product_image_strain_assign, which writes a shared platform strain CDN URL and never creates a tenant library row (GitHub #72 / #184).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_image_assign_request = new \ShadowSoftware\DabDash\Model\ProductImageAssignRequest(); // \ShadowSoftware\DabDash\Model\ProductImageAssignRequest

try {
    $result = $apiInstance->productImageAssign($product_image_assign_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productImageAssign: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_image_assign_request** | [**\ShadowSoftware\DabDash\Model\ProductImageAssignRequest**](../Model/ProductImageAssignRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductImageAssign200Response**](../Model/ProductImageAssign200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productImageStrainAssign()`

```php
productImageStrainAssign($product_image_strain_assign_request): \ShadowSoftware\DabDash\Model\ProductImageStrainAssign200Response
```

Apply a hosted platform strain photo to a product's featured_image. Writes a URL string to the product row only — never uploads/copies bytes, never creates a tenant media_assets row (this is the shared strain library, not tenant media).  Two modes (exactly one required):   assignments: explicit [{product_id, strain_id}, ...] pairs you already     decided on (e.g. from product_image_strain_match's proposals).   auto_match: true — re-runs product_image_strain_match's own matching     internally against products missing a featured image (optionally     scoped by search/limit) and applies every exact/partial match found.  dry_run (default true) previews without writing — always call once with dry_run=true and review the results before dry_run=false.  overwrite (default false) — a product that already has a featured_image is skipped, never replaced, unless overwrite=true. Idempotent: if a product's featured_image already equals the target strain's hosted URL, it is reported skipped/already_set regardless of overwrite (nothing to do, not an error).  Refuses to assign a strain whose image is not hosted on the platform (skipped/strain_image_not_hosted) — never writes a dead or third-party hotlinked URL as a product's featured image.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_image_strain_assign_request = new \ShadowSoftware\DabDash\Model\ProductImageStrainAssignRequest(); // \ShadowSoftware\DabDash\Model\ProductImageStrainAssignRequest

try {
    $result = $apiInstance->productImageStrainAssign($product_image_strain_assign_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productImageStrainAssign: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_image_strain_assign_request** | [**\ShadowSoftware\DabDash\Model\ProductImageStrainAssignRequest**](../Model/ProductImageStrainAssignRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductImageStrainAssign200Response**](../Model/ProductImageStrainAssign200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productManage()`

```php
productManage($product_manage_request): \ShadowSoftware\DabDash\Model\ProductManage200Response
```

Create a product for a tenant. With no variations this creates a simple one-price product, matching a blank create-product page. For named options, pass the complete variations list and expected_variation_count; the product is created as unit variation inventory and the pricing tiers are kept in sync. Call strain_lookup first when the name looks like a strain, then pass strain_id so description, type, THC/CBD, indica/sativa, effects, flavors, rating, and the hosted strain photo fill in exactly like the vendor create page.  If strain_id is omitted, this tool name-matches the platform strain library itself: an exact name match is applied automatically; close matches are returned as strain_matches so you can offer them. Pass skip_strain_enrich=true to skip that.  ACTIONS:   create: requires name. Optional price (dollars), sku, stock_quantity,           category_ids, description, strain_id, or a complete variations list. update: requires product_id. Pass any of name, description, strain_id,           category_ids, meta_title, or meta_description to groom an existing           product without changing its price.           Strain enrichment never replaces an existing product photo; use           product_image_assign with a tenant media_id for photos.           meta_title max 60 chars; meta_description max 160 chars.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_manage_request = new \ShadowSoftware\DabDash\Model\ProductManageRequest(); // \ShadowSoftware\DabDash\Model\ProductManageRequest

try {
    $result = $apiInstance->productManage($product_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_manage_request** | [**\ShadowSoftware\DabDash\Model\ProductManageRequest**](../Model/ProductManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductManage200Response**](../Model/ProductManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productUpdateBySku()`

```php
productUpdateBySku($product_update_by_sku_request): \ShadowSoftware\DabDash\Model\ProductUpdateBySku200Response
```

Update a simple product's stock quantity and/or price by SKU — the inventory-sync path for an external POS. v1 scope: SIMPLE products only (single implicit unit, no weight/variant tiers). Every other pricing type (weight, unit, matrix, matrix_unit) is rejected with a clear message; those need per-tier/per-variant targeting that a flat SKU+quantity+price payload cannot express safely. Always call product_inspect with sku first to confirm which product/type you are targeting.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_update_by_sku_request = new \ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest(); // \ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest

try {
    $result = $apiInstance->productUpdateBySku($product_update_by_sku_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productUpdateBySku: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_update_by_sku_request** | [**\ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest**](../Model/ProductUpdateBySkuRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductUpdateBySku200Response**](../Model/ProductUpdateBySku200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productVariationSync()`

```php
productVariationSync($product_variation_sync_request): \ShadowSoftware\DabDash\Model\ProductVariationSync200Response
```

Atomically create or reconcile the complete variation list and per-variation inventory for one product. Use this for unit or matrix products after pricing is established. Choose unit for one-dimensional named variants and matrix_unit only for a real name/flavour × quantity/pack-size grid. The payload must contain every variant, an exact expected_variation_count, price, cost_price, and stock_quantity for every row. It is idempotent: matching existing IDs or names are updated, missing rows are created, and existing rows are never deleted. dry_run defaults to true. A successful live run returns before/after counts and stock totals for verification. Weight sizes of one flavor share one gram count — write that count on each size, and never describe it as extra inventory. Pack sizes are separate stock. If the vendor gives one count for a flavor, ask which pack size it belongs to before copying it onto every size. Never split one product across multiple calls.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_variation_sync_request = new \ShadowSoftware\DabDash\Model\ProductVariationSyncRequest(); // \ShadowSoftware\DabDash\Model\ProductVariationSyncRequest

try {
    $result = $apiInstance->productVariationSync($product_variation_sync_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productVariationSync: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_variation_sync_request** | [**\ShadowSoftware\DabDash\Model\ProductVariationSyncRequest**](../Model/ProductVariationSyncRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductVariationSync200Response**](../Model/ProductVariationSync200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productVisibilityManage()`

```php
productVisibilityManage($product_visibility_manage_request): \ShadowSoftware\DabDash\Model\ProductVisibilityManage200Response
```

Set whether tenant products are public or inventory-only without changing inventory, cost, price, category assignments, images, or variations.  visibility=public maps to product status=active. visibility=inventory_only maps to status=archived, which removes the product from storefront product, category, related-product, and sitemap queries while preserving the product and its inventory for vendor use. This is reversible.  dry_run defaults to true. A write requires dry_run=false and confirm=true. Always preview first. Only the explicitly listed product_ids are considered.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_visibility_manage_request = new \ShadowSoftware\DabDash\Model\ProductVisibilityManageRequest(); // \ShadowSoftware\DabDash\Model\ProductVisibilityManageRequest

try {
    $result = $apiInstance->productVisibilityManage($product_visibility_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productVisibilityManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_visibility_manage_request** | [**\ShadowSoftware\DabDash\Model\ProductVisibilityManageRequest**](../Model/ProductVisibilityManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductVisibilityManage200Response**](../Model/ProductVisibilityManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `purchaseOrderDraftCreate()`

```php
purchaseOrderDraftCreate($purchase_order_draft_create_request): \ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreate200Response
```

Create a draft purchase order for a supplier. Does not receive stock. After creating, add lines with purchase_order_line_add, then tell the vendor to review the draft in admin (never auto-receive).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$purchase_order_draft_create_request = new \ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreateRequest(); // \ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreateRequest

try {
    $result = $apiInstance->purchaseOrderDraftCreate($purchase_order_draft_create_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->purchaseOrderDraftCreate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **purchase_order_draft_create_request** | [**\ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreateRequest**](../Model/PurchaseOrderDraftCreateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreate200Response**](../Model/PurchaseOrderDraftCreate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `purchaseOrderLineAdd()`

```php
purchaseOrderLineAdd($purchase_order_line_add_request): \ShadowSoftware\DabDash\Model\PurchaseOrderLineAdd200Response
```

Add a product line to a draft purchase order. Pass product_id (and variation_id when the product tracks stock by size). qty is the ordered quantity. unit_cost is dollars per unit; omit to use last cost.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$purchase_order_line_add_request = new \ShadowSoftware\DabDash\Model\PurchaseOrderLineAddRequest(); // \ShadowSoftware\DabDash\Model\PurchaseOrderLineAddRequest

try {
    $result = $apiInstance->purchaseOrderLineAdd($purchase_order_line_add_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->purchaseOrderLineAdd: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **purchase_order_line_add_request** | [**\ShadowSoftware\DabDash\Model\PurchaseOrderLineAddRequest**](../Model/PurchaseOrderLineAddRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PurchaseOrderLineAdd200Response**](../Model/PurchaseOrderLineAdd200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `reviewsManage()`

```php
reviewsManage($reviews_manage_request): \ShadowSoftware\DabDash\Model\ReviewsManage200Response
```

Show or configure verified-purchase product reviews for a tenant, and preview or apply moderation changes to one review. Customers can review products only after a delivered order. Hidden reviews are excluded from the product rating.  ACTIONS:   show (default): current review setting and moderation counts.   set: pass enabled to turn review collection on or off. dry_run defaults true.   hide or unhide: pass review_id. dry_run defaults true; applying a moderation   change also recalculates that product's rating.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$reviews_manage_request = new \ShadowSoftware\DabDash\Model\ReviewsManageRequest(); // \ShadowSoftware\DabDash\Model\ReviewsManageRequest

try {
    $result = $apiInstance->reviewsManage($reviews_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->reviewsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **reviews_manage_request** | [**\ShadowSoftware\DabDash\Model\ReviewsManageRequest**](../Model/ReviewsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ReviewsManage200Response**](../Model/ReviewsManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `seoMaxImageManage()`

```php
seoMaxImageManage($seo_max_image_manage_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Manage the SEO Max product-image rollout without Artisan commands or seed data. Use action=status first. action=configure toggles AVIF product delivery and the AVIF-only image sitemap. action=backfill processes active products in bounded, resumable batches; dry_run=true only reports work. Repeat with next_cursor as after_id until it is null. action=submit notifies Google Search Console and IndexNow after the sitemap is live.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$seo_max_image_manage_request = new \ShadowSoftware\DabDash\Model\SeoMaxImageManageRequest(); // \ShadowSoftware\DabDash\Model\SeoMaxImageManageRequest

try {
    $result = $apiInstance->seoMaxImageManage($seo_max_image_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->seoMaxImageManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **seo_max_image_manage_request** | [**\ShadowSoftware\DabDash\Model\SeoMaxImageManageRequest**](../Model/SeoMaxImageManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `seoSettingsManage()`

```php
seoSettingsManage($seo_settings_manage_request): \ShadowSoftware\DabDash\Model\SeoSettingsManage200Response
```

Show or update a tenant's standard SEO and SEO MAX settings. Standard fields control homepage metadata, title templates, sitemap, Open Graph, and Twitter metadata. SEO MAX fields control automated blog targeting and publishing, plus image delivery flags.  ACTIONS:   show (default): current values, including the stored featured-image canvas path.   set: pass only fields to change. dry_run defaults true; pass dry_run=false to save. Enabling SEO MAX requires an active SEO MAX subscription or trial and complete schedule settings. Featured-image canvas uploads are handled by media tools and are read-only here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$seo_settings_manage_request = new \ShadowSoftware\DabDash\Model\SeoSettingsManageRequest(); // \ShadowSoftware\DabDash\Model\SeoSettingsManageRequest

try {
    $result = $apiInstance->seoSettingsManage($seo_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->seoSettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **seo_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\SeoSettingsManageRequest**](../Model/SeoSettingsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SeoSettingsManage200Response**](../Model/SeoSettingsManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `smartListManage()`

```php
smartListManage($smart_list_manage_request): \ShadowSoftware\DabDash\Model\SmartListManage200Response
```

Create, edit, preview, or delete Smart Lists (dynamic customer audiences).  ACTIONS:   list / show — discover Smart Lists (also available via contact_list_manage).   upsert — create (omit list_id) or update. Requires name + definition     {version:1, match:\"all\"|\"any\", conditions:[...]}.     Rule types: purchased_category, recent_category_buyers, lapsed_category_buyers,     not_purchased_category, purchased_product, purchase_count, spend_total,     top_spenders, dormant_customer, loyalty_lifetime_points, loyalty_tier.   preview — count matching customers for a definition (or existing list_id).   delete — dry_run default true; applying needs dry_run=false + confirm=true.  Attach Smart Lists to campaigns with campaign_upsert.contact_list_ids or to coupons via coupon_upsert.contact_list_id. Project to Klaviyo with tenant_klaviyo_manage.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$smart_list_manage_request = new \ShadowSoftware\DabDash\Model\SmartListManageRequest(); // \ShadowSoftware\DabDash\Model\SmartListManageRequest

try {
    $result = $apiInstance->smartListManage($smart_list_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->smartListManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **smart_list_manage_request** | [**\ShadowSoftware\DabDash\Model\SmartListManageRequest**](../Model/SmartListManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SmartListManage200Response**](../Model/SmartListManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `storeSettingsManage()`

```php
storeSettingsManage($store_settings_manage_request): \ShadowSoftware\DabDash\Model\StoreSettingsManage200Response
```

Show or update a store's core settings: display name, phone, address, coordinates, timezone, currency, country, locale, brand colours, and opening hours.  ACTIONS:   show (default): current values.   set: pass only the fields to change. dry_run defaults true — the first call        reports the diff without writing; pass dry_run=false to save.  Brand colours: theme_color is the primary, accent_color the secondary (both #RRGGBB; a 3-digit #RGB accent is expanded). button_text_color is auto (default: white or near-black, whichever contrasts more), white, or dark. Every response includes `palette`: the effective text colour on primary/accent, its WCAG contrast ratio, and warnings for any pairing under 4.5:1 — dry-run first to preview a palette.  opening_hours is a list of {days: [\"Monday\", ...], opens: \"09:00\", closes: \"21:00\"} rows (24h HH:MM, full English day names). Passing it replaces all rows; pass an empty list to clear. Hours feed the storefront About page and search-engine markup. storefront_theme picks the storefront look: classic (default) or modern. theme_mode controls the storefront colour mode: light or dark. hero_* fields preserve the vendor's full hero message and modern-theme content. The Modern hero featured-product card is optional: hero_spotlight_mode is auto (newest sale), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. Badge, caption, and button are hero_spotlight_label, hero_spotlight_kicker, and hero_spotlight_cta. product_feature_manage only toggles the separate Featured Products grid. The same card and the rest of the Modern homepage/footer are also on storefront_theme_manage. Vendors edit the card in Settings → Theme. hero_ticker_items replaces custom ticker rows with {label, tag, url} objects; URLs must be site paths or https:// links. Use hero_ticker_mode=custom to show them. hero_status_mode controls the Modern hero online/last-call pill: hours (from opening_hours), auto (delivery slots when available, else hours), or off. minimum_age is the storefront age gate / About compliance age (e.g. 19 in NS). footer_notes and about_* set Modern footer notes and About intro copy. privacy_url is the authoritative published privacy-policy URL. Use the exact blog URL when the policy is a blog post. delivery_meta_description is an optional, tenant-specific description for the public /delivery page. Keep it factual and 160 characters or fewer. social_facebook / social_instagram / social_twitter / social_x / google_business_url / gbp_url are absolute https URLs published as Organization and LocalBusiness sameAs (Knowledge Panel alignment). freebie_cost_in_cogs (default true) includes giveaway freebie wholesale cost in vendor COGS / margin reports even though freebie revenue is $0. revenue_attribution_date is placed (order checkout day) or delivered (scheduled delivery calendar day) for analytics revenue dating. Slug, owner, subscription, and custom domain are not changed here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$store_settings_manage_request = new \ShadowSoftware\DabDash\Model\StoreSettingsManageRequest(); // \ShadowSoftware\DabDash\Model\StoreSettingsManageRequest

try {
    $result = $apiInstance->storeSettingsManage($store_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->storeSettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **store_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\StoreSettingsManageRequest**](../Model/StoreSettingsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\StoreSettingsManage200Response**](../Model/StoreSettingsManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `storefrontThemeManage()`

```php
storefrontThemeManage($storefront_theme_manage_request): \ShadowSoftware\DabDash\Model\StorefrontThemeManage200Response
```

Show or update Modern storefront theme data: hero copy, the optional hero featured-product card, deals ticker, homepage sections, footer, and About cards.  The hero card is optional. hero_spotlight_mode is auto (newest sale product), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. product_feature_manage only toggles the separate Featured Products grid. Vendors edit the same card in Settings → Theme → Hero features.  ACTIONS:   show (default): current saved fields. Unset custom text is null and uses the     storefront's shipped fallback; toggle values include their effective defaults.   set: pass only fields to change. Omitted fields are preserved. Empty text clears     the custom value. about_cards replaces the full card list. hero_ticker_items     replaces the custom ticker.   dry_run defaults true; pass dry_run=false to save.  The WhatsApp number is left untouched unless whatsapp_number is explicitly passed.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$storefront_theme_manage_request = new \ShadowSoftware\DabDash\Model\StorefrontThemeManageRequest(); // \ShadowSoftware\DabDash\Model\StorefrontThemeManageRequest

try {
    $result = $apiInstance->storefrontThemeManage($storefront_theme_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->storefrontThemeManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **storefront_theme_manage_request** | [**\ShadowSoftware\DabDash\Model\StorefrontThemeManageRequest**](../Model/StorefrontThemeManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\StorefrontThemeManage200Response**](../Model/StorefrontThemeManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `supplierUpsert()`

```php
supplierUpsert($supplier_upsert_request): \ShadowSoftware\DabDash\Model\SupplierUpsert200Response
```

Create or update a product supplier for purchase orders.  UPDATE MODE (supplier_id): only the fields you pass are changed. CREATE MODE (no supplier_id): name is required. If a supplier with the same name already exists for this vendor, that row is updated instead.  Always resolve the supplier_id before purchase_order_draft_create.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$supplier_upsert_request = new \ShadowSoftware\DabDash\Model\SupplierUpsertRequest(); // \ShadowSoftware\DabDash\Model\SupplierUpsertRequest

try {
    $result = $apiInstance->supplierUpsert($supplier_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->supplierUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **supplier_upsert_request** | [**\ShadowSoftware\DabDash\Model\SupplierUpsertRequest**](../Model/SupplierUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SupplierUpsert200Response**](../Model/SupplierUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `suppressionManage()`

```php
suppressionManage($suppression_manage_request): \ShadowSoftware\DabDash\Model\SuppressionManage200Response
```

Inspect or lift email suppressions / campaign unsubscribes for a tenant.  ACTIONS:   list — recent suppressed customers (email_opt_out) and unsubscribed contacts.   lift_transactional — clear customer email_opt_out for an email.   resubscribe — restore contact.subscribed for campaign lists.   lift_all — both transactional + campaign for one email.  Mutating actions: dry_run default true; apply needs dry_run=false + confirm=true.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$suppression_manage_request = new \ShadowSoftware\DabDash\Model\SuppressionManageRequest(); // \ShadowSoftware\DabDash\Model\SuppressionManageRequest

try {
    $result = $apiInstance->suppressionManage($suppression_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->suppressionManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **suppression_manage_request** | [**\ShadowSoftware\DabDash\Model\SuppressionManageRequest**](../Model/SuppressionManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SuppressionManage200Response**](../Model/SuppressionManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantBlogUpsert()`

```php
tenantBlogUpsert($tenant_blog_upsert_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Update an existing tenant (vendor) blog post's fields — title, excerpt, body, category, featured_image, meta_title, meta_description, og_image, or is_published. Only the fields you pass are changed. Use tenant_blog_lookup first to find the post_id or slug.  IMAGE: pass media_id (from media_list / media_upload) to set featured_image from a tenant media-library asset. Raw featured_image URLs/paths are refused — ingest with media_upload first, then place by media_id (GitHub #184). Pass featured_image empty/null to clear. A superseded image this post previously owned is cleaned up from disk automatically once the change is saved.  dry_run (default true) previews the fields that would change without writing — call once with dry_run=true and review before dry_run=false.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_blog_upsert_request = new \ShadowSoftware\DabDash\Model\TenantBlogUpsertRequest(); // \ShadowSoftware\DabDash\Model\TenantBlogUpsertRequest

try {
    $result = $apiInstance->tenantBlogUpsert($tenant_blog_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantBlogUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_blog_upsert_request** | [**\ShadowSoftware\DabDash\Model\TenantBlogUpsertRequest**](../Model/TenantBlogUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantBrandingManage()`

```php
tenantBrandingManage($tenant_branding_manage_request): \ShadowSoftware\DabDash\Model\TenantBrandingManage200Response
```

Show or update a tenant's store logo, homepage hero image, and BIMI email logo, then upsert the Cloudflare `default._bimi` TXT record when the zone is connected.  ACTIONS:   show (default): current logo_path, storefront logo URL (custom domain when          set), hero_image_path/URL, BIMI path/URL, the TXT value to publish,          and whether Cloudflare is connected.   set: pass media_id (from media_list / media_upload) to point logo_path at a          library asset. Pass hero_media_id for settings.hero_image_path, or          clear_hero=true to remove the hero. Pass exactly one of source_base64 /          source_path / source_url for an SVG to store the BIMI logo at          tenants/{id}/bimi-logo.svg (media_upload cannot take SVG). dry_run          defaults true — the first call reports what would happen.  Logo URLs and BIMI `l=` values always use the tenant storefront host (custom domain or {slug}.dabdash.com), never the platform APP_URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_branding_manage_request = new \ShadowSoftware\DabDash\Model\TenantBrandingManageRequest(); // \ShadowSoftware\DabDash\Model\TenantBrandingManageRequest

try {
    $result = $apiInstance->tenantBrandingManage($tenant_branding_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantBrandingManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_branding_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantBrandingManageRequest**](../Model/TenantBrandingManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantBrandingManage200Response**](../Model/TenantBrandingManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantDomainManage()`

```php
tenantDomainManage($tenant_domain_manage_request): \ShadowSoftware\DabDash\Model\TenantDomainManage200Response
```

Show, connect, verify, or remove a store's custom domain (e.g. maritimesmoke.com).  ACTIONS:   show (default): current domain, verified flag, and the DNS TXT record to add.   set: connect a domain (bare hostname, no http://). Resets verification. dry_run        defaults true; pass dry_run=false to save.   verify: checks the TXT record or the storefront domain-check URL and marks the        domain verified. Run after DNS has been changed.   remove: disconnect the custom domain. dry_run defaults true.  DNS records are not created here; show returns what to add at the registrar or in Cloudflare. Until verified the store keeps working on {slug}.dabdash.com.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_domain_manage_request = new \ShadowSoftware\DabDash\Model\TenantDomainManageRequest(); // \ShadowSoftware\DabDash\Model\TenantDomainManageRequest

try {
    $result = $apiInstance->tenantDomainManage($tenant_domain_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantDomainManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_domain_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantDomainManageRequest**](../Model/TenantDomainManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantDomainManage200Response**](../Model/TenantDomainManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantKlaviyoManage()`

```php
tenantKlaviyoManage($tenant_klaviyo_manage_request): \ShadowSoftware\DabDash\Model\TenantKlaviyoManage200Response
```

Show or manage a tenant's BYO Klaviyo integration (profiles, list sync, Smart List projection, order events). Prefer guiding vendors to Settings → Integrations.  ACTIONS:   show (default): connection status + settings (never the API key).   connect / disconnect: dry_run default true; apply needs dry_run=false+confirm.   update_settings: default_list_id, mapped_contact_list_id, smart_list_ids,     smart_list_sync_enabled.   sync_now / refresh_smart_lists / use_for_orders / available_lists.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_klaviyo_manage_request = new \ShadowSoftware\DabDash\Model\TenantKlaviyoManageRequest(); // \ShadowSoftware\DabDash\Model\TenantKlaviyoManageRequest

try {
    $result = $apiInstance->tenantKlaviyoManage($tenant_klaviyo_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantKlaviyoManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_klaviyo_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantKlaviyoManageRequest**](../Model/TenantKlaviyoManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantKlaviyoManage200Response**](../Model/TenantKlaviyoManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantMailgunManage()`

```php
tenantMailgunManage($tenant_mailgun_manage_request): \ShadowSoftware\DabDash\Model\TenantMailgunManage200Response
```

Show or manage a tenant's Mailgun Send Email setup (campaigns + hybrid SMTP).  Prefer guiding vendors to Settings → Domain & Email → Send Email (/admin/settings/domain#send-email). That is the canonical setup home — not Integrations alone.  ACTIONS:   show (default): connection status, Ready sending names, SMTP bootstrap     flag, and the #send-email setup URL. Never returns the API key.   connect: validate + store a Mailgun private key. dry_run defaults true;     applying requires dry_run=false and confirm=true. Dry-run still     validates the key with Mailgun (read-only).   disconnect: remove the Mailgun integration row (SMTP left alone).     Applying requires dry_run=false and confirm=true.   sync: import domains from the Mailgun account into local rows.   add_domain: create/reuse one sending name under the store/CF apex.   verify_domain: ask Mailgun to re-check DNS for one sending name.   autoconfigure: create mail1..mail5 under the Cloudflare zone and push DNS.     Applying requires dry_run=false and confirm=true.   bootstrap_smtp: fill tenants.settings.smtp from a Ready Mailgun domain.     Applying requires dry_run=false; force_smtp=true also needs confirm=true.  Mutating actions default dry_run=true; pass dry_run=false to apply.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_mailgun_manage_request = new \ShadowSoftware\DabDash\Model\TenantMailgunManageRequest(); // \ShadowSoftware\DabDash\Model\TenantMailgunManageRequest

try {
    $result = $apiInstance->tenantMailgunManage($tenant_mailgun_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantMailgunManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_mailgun_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantMailgunManageRequest**](../Model/TenantMailgunManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantMailgunManage200Response**](../Model/TenantMailgunManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantSmtpManage()`

```php
tenantSmtpManage($tenant_smtp_manage_request): \ShadowSoftware\DabDash\Model\TenantSmtpManage200Response
```

Show or update Advanced store SMTP (manual host/user/password).  Prefer Mailgun Send Email for campaigns and easiest setup: use tenant_mailgun_manage, and send vendors to /admin/settings/domain#send-email. This tool is for Advanced / BYO SMTP only.  ACTIONS:   show (default): returns the current SMTP settings with the password omitted.   set: updates only the supplied fields. dry_run defaults true; pass dry_run=false        to save. smtp_password is write-only and is never returned.  Keep from_address equal to the authenticated SMTP username unless the provider has explicitly authorized a different sender or alias. A successful settings update clears the tenant's stale SMTP health banner; the next send records any still-current failure.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_smtp_manage_request = new \ShadowSoftware\DabDash\Model\TenantSmtpManageRequest(); // \ShadowSoftware\DabDash\Model\TenantSmtpManageRequest

try {
    $result = $apiInstance->tenantSmtpManage($tenant_smtp_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantSmtpManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_smtp_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantSmtpManageRequest**](../Model/TenantSmtpManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantSmtpManage200Response**](../Model/TenantSmtpManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantTwilioManage()`

```php
tenantTwilioManage($tenant_twilio_manage_request): \ShadowSoftware\DabDash\Model\TenantTwilioManage200Response
```

Show or manage a tenant's Twilio SMS connection (required for text campaigns). Prefer Settings → Integrations.  ACTIONS: show | connect | disconnect | test. connect needs account_sid + auth_token + from_number or messaging_service_sid. Mutating actions: dry_run default true; apply needs dry_run=false + confirm=true.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_twilio_manage_request = new \ShadowSoftware\DabDash\Model\TenantTwilioManageRequest(); // \ShadowSoftware\DabDash\Model\TenantTwilioManageRequest

try {
    $result = $apiInstance->tenantTwilioManage($tenant_twilio_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantTwilioManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_twilio_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantTwilioManageRequest**](../Model/TenantTwilioManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantTwilioManage200Response**](../Model/TenantTwilioManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `webhookManage()`

```php
webhookManage($webhook_manage_request): \ShadowSoftware\DabDash\Model\WebhookManage200Response
```

List, create, update, or delete saved tenant webhooks (order events + campaign webhook mode).  ACTIONS: list | show | upsert | delete. upsert needs label + url (https). Optional health_url, events (order.created|order.shipped), is_active, custom_headers [{name,value}]. Signing secrets are generated server-side and returned once on create. Klaviyo-managed webhooks cannot be edited here — use tenant_klaviyo_manage. Assign to a campaign with campaign_upsert.saved_webhook_id.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$webhook_manage_request = new \ShadowSoftware\DabDash\Model\WebhookManageRequest(); // \ShadowSoftware\DabDash\Model\WebhookManageRequest

try {
    $result = $apiInstance->webhookManage($webhook_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->webhookManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **webhook_manage_request** | [**\ShadowSoftware\DabDash\Model\WebhookManageRequest**](../Model/WebhookManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\WebhookManage200Response**](../Model/WebhookManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `widgetManage()`

```php
widgetManage($widget_manage_request): \ShadowSoftware\DabDash\Model\WidgetManage200Response
```

List, create, update, or delete a tenant's homepage marketing widgets (the hero slider cards linking to a product, category, featured products, or a mix & match tag).  ACTIONS:   list   (default): return every widget with id, title, subtitle, link_type, target, sort_order,          is_active, and image_url. Always call this first to find a widget_id.   create: requires title. link_type + its matching id/tag is optional but recommended so the          widget's CTA actually goes somewhere (see LINK_TYPE below). Defaults to \"featured\"          (no target) when omitted.   update: requires widget_id. Only the fields you pass are changed.   delete: requires widget_id and confirm=true.  LINK_TYPE — pairs with exactly one target field:   \"product\"    → product_id   \"category\"   → category_id   \"mix_match\"  → mix_match_tag   \"featured\"   → no target needed (links to the featured-products listing)  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites the headline/logo overlay onto). Omit both to leave images untouched.  Always call action=list first to confirm widget_id before update or delete.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$widget_manage_request = new \ShadowSoftware\DabDash\Model\WidgetManageRequest(); // \ShadowSoftware\DabDash\Model\WidgetManageRequest

try {
    $result = $apiInstance->widgetManage($widget_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->widgetManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **widget_manage_request** | [**\ShadowSoftware\DabDash\Model\WidgetManageRequest**](../Model/WidgetManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\WidgetManage200Response**](../Model/WidgetManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `zoneClone()`

```php
zoneClone($zone_clone_request): \ShadowSoftware\DabDash\Model\ZoneClone200Response
```

Clone every polygon zone from a source tenant into the target tenant while preserving the target tenant's existing fee tiers. This is intended for importing a proven coverage map without importing another store's pricing.  The source zones are grouped into tiers by their minimum order, then mapped by rank to the target tenant's existing zones ordered by minimum order. The target tier's delivery_fee, service_fee, min_order, color, priority, and fulfillment_type are applied to every cloned source polygon. Existing target zones are replaced only when replace_existing=true and confirm=true. Zone colors use an evenly spaced spectrum from the target primary colour to its accent colour, one stop per fee rung. dry_run defaults true.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$zone_clone_request = new \ShadowSoftware\DabDash\Model\ZoneCloneRequest(); // \ShadowSoftware\DabDash\Model\ZoneCloneRequest

try {
    $result = $apiInstance->zoneClone($zone_clone_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->zoneClone: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **zone_clone_request** | [**\ShadowSoftware\DabDash\Model\ZoneCloneRequest**](../Model/ZoneCloneRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ZoneClone200Response**](../Model/ZoneClone200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `zoneManage()`

```php
zoneManage($zone_manage_request): \ShadowSoftware\DabDash\Model\ZoneManage200Response
```

List or delete a tenant's delivery zones.  ACTIONS:   list   (default): return every zone with id, name, slug, is_active, is_example,          fulfillment_type, priority, delivery_fee, service_fee, min_order, and counts          of related orders/customer addresses/delivery slots. Always call this first          to find zone_ids and confirm which zones are welcome-wizard example/template          zones (is_example=true, or slug prefixed welcome-/example-) versus real          vendor-configured coverage, before deleting anything.    delete: requires zone_ids + confirm=true. Refuses to delete a zone that still has          orders, saved customer addresses, or delivery slots attached unless          force=true is passed (force does not delete those related rows — it only          lets the zone row be removed; related rows keep their zone_id, which becomes          orphaned, so prefer reassigning/clearing them first). dry_run defaults true —          the first call always reports what would happen without writing.  Common use: cleaning up leftover onboarding-wizard example zones (the three \"N km delivery area\" circles and/or the \"Downtown\" demo zone, slugs welcome-5km / welcome-10km / welcome-15km / example-downtown) when a vendor ended up with both those AND real GADM administrative-boundary zones after running the wizard's circle and boundary-import steps in sequence.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$zone_manage_request = new \ShadowSoftware\DabDash\Model\ZoneManageRequest(); // \ShadowSoftware\DabDash\Model\ZoneManageRequest

try {
    $result = $apiInstance->zoneManage($zone_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->zoneManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **zone_manage_request** | [**\ShadowSoftware\DabDash\Model\ZoneManageRequest**](../Model/ZoneManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ZoneManage200Response**](../Model/ZoneManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `zoneUpsert()`

```php
zoneUpsert($zone_upsert_request): \ShadowSoftware\DabDash\Model\ZoneUpsert200Response
```

Create or update a delivery/pickup/shipping zone. Use zone_manage to list or delete.  CREATE (no zone_id): name is required; a zone with the same name is updated instead. UPDATE (zone_id): only the fields you pass change.  Coverage shape — pass ONE of:   polygon: list of {lat, lng} points (or [lng, lat] pairs), at least 3, at most 500.   center_lat + center_lng + radius_km: builds a 32-point circle. Good enough when            the client only publishes a list of towns; refine the polygon later. Fees and minimum order are in dollars. dry_run defaults true: the first call reports what would be saved; pass dry_run=false to write.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$zone_upsert_request = new \ShadowSoftware\DabDash\Model\ZoneUpsertRequest(); // \ShadowSoftware\DabDash\Model\ZoneUpsertRequest

try {
    $result = $apiInstance->zoneUpsert($zone_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->zoneUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **zone_upsert_request** | [**\ShadowSoftware\DabDash\Model\ZoneUpsertRequest**](../Model/ZoneUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ZoneUpsert200Response**](../Model/ZoneUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
